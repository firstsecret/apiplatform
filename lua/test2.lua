--
-- Created by IntelliJ IDEA.
-- User: Admin
-- Date: 2018/11/7
-- Time: 11:21
-- To change this template use File | Settings | File Templates.
--

local hc = require "resty.upstream.healthcheck"
local redis_client = require('resty.redis_client')
local redis = redis_client:new()

--
local ok, err = hc.spawn_checker {
    shm = "healthcheck",
    upstream = "api.com",
    type = "http",
    http_req = "GET / HTTP/1.1\r\nHost: api.com\r\n\r\n",
    interval = 5000,
    --    timeout = 1000,
    fall = 3,
    --rise = 2,
    --    valid_statuses = {200, 302},
    concurrency = 10,
}

if not ok then
    ngx.log(ngx.ERR, "[health check]failed to spawn health checker: ", err)
    return
end

local page_str = hc.status_page()
-- status handle
if ngx.var.flag and ngx.var.flag ~= ngx.null and ngx.var.flag ~= '' then

    --    ngx.say("Nginx Worker PID: ", ngx.worker.pid())
    --        ngx.print(page_str)
end

local iterator, err = ngx.re.gmatch(page_str, "([0-9]+\\.[0-9]+\\.[0-9]+\\.[0-9]+:[0-9]+)\\s+(\\w+)+", "jo")

if not iterator then
    -- m[0] == "4001493201083"\
    ngx.log(ngx.ERR, "[health_check] error: iterator match err, ", err)
    --    ngx.say("makcj::",m[0])
    --    ngx.say(cjson.encode(m))
    return
end

while true do
    local m, err_ = iterator()
    --    ngx.say('ddddd')
    if err_ then
        ngx.log(ngx.ERR, "[health_check] error: ", err_)

        return
    end
    if not m then
        -- no match found (any more)
        break
    end
    -- found a match

    -- redis
    if string.lower(m[2]) == 'up' then
        redis:exec(function(red) red:SADD('node_load_balancing', m[1]) end)
    else
        redis:exec(function(red) red:SREM('node_load_balancing', m[1]) end)
    end
end

--if from then
--    ngx.say("from: ", from)
--    ngx.say("to: ", to)
--    ngx.say("matched: ", string.sub(page_str, from, to))
--else
--    if err then
--        ngx.say("error: ", err)
--    end
--    ngx.say("not matched!")
--end

