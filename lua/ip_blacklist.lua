--
-- ip blacklist
-- User: Bevan
-- Date: 2018/9/19
-- Time: 9:20
-- To change this template use File | Settings | File Templates.
--

local redis_host = '127.0.0.1'
local redis_port = '6379'

-- don't too long
local redis_connection_timeout = 100

local redis_key = 'ip_blacklist'

-- cache ttl
local cache_ttl = 60

local ip = ngx.var.remote_addr
local ip_blacklist = ngx.shared.ip_blacklist
local last_update_time = ip_blacklist:get('last_update_time')

-- is overtime
if (last_update_time == nil or last_update_time < (ngx.now() - cache_ttl)) then

    local redis = require "resty.redis"
    local red = redis:new()

    red:set_timeout(redis_connection_timeout)

    local ok, err = red:connect(redis_host, redis_port)

    if not ok then
        ngx.log(ngx.CRIT, "Redis Connect error while retrieving ip_blacklist: " .. err)
    else
        local new_ip_blacklist, err = red:smembers(redis_key)

        if (err) then
            ngx.log(ngx.CRIT, "Reids Read error while retrieving ip_bliacklist:" .. err)
        else
            ip_blacklist:flush_all()
            for index, banned_ip in ipairs(new_ip_blacklist) do
                ip_blacklist:set(banned_ip, true)
            end
            -- update time
            ip_blacklist:set('last_update_time', ngx.now())
        end
    end
end

--
if ip_blacklist:get(ip) then
    ngx.log(ngx.CRIT, "Banned IP detected and refused access: " .. ip)
    return ngx.exit(ngx.HTTP_NOT_ALLOWED )
end
