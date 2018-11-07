--
-- Created by IntelliJ IDEA.
-- User: Admin
-- Date: 2018/10/15
-- Time: 16:41
-- To change this template use File | Settings | File Templates.
--

--local request_uri = ngx.var.request_uri
--local regex = [[/api/(.*)?]]
--local m = ngx.re.match(request_uri, '/api/(.*)?', "jo")
--
--if m then
--    ngx.print(m[0])
--else
--    ngx.print('未匹配')
--end


--local tool = require "resty.tool"
--local cjson = require "cjson"
--local redis = require "resty.redis_client"
--
--
--
--ngx.print(cjson.encode(res))
--local app_key = 'api_app_key:439d8c975f26e5005dcdbf41b0d84161'
--local red = redis.new()
--local res, err = red:exec(function(red)
--    local info = red:hgetall(app_key)
--    local rt = {}
--
--    for i = 1, #info, 2 do
--        rt[info[i]] = info[i + 1]
--    end
--    --            ngx.print(cjson.encode(rt))
--    return rt
--end)
--
--ngx.print(cjson.encode(res))

local hc = require "resty.upstream.healthcheck"

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
if ngx.var.flag then
--    ngx.say("Nginx Worker PID: ", ngx.worker.pid())
    ngx.print(page_str)
end

-- preg
