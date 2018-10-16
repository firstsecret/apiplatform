--
-- healthy check.
-- User: Bevan
-- Date: 2018/10/15
-- Time: 14:51
-- To change this template use File | Settings | File Templates.
--

local hc = require "resty.upstream.healthcheck"

local ok, err = hc.spawn_checker {
    shm = "healthcheck",
    upstream = "api.com",
    type = "http",

    http_req = "GET /healthcheck.txt HTTP/1.1\r\nHost: api.com\r\n\r\n",
    interval = 2000,
    --timeout = 2000,
    fall = 3,
    --rise = 2,
    --valid_statuses = {200, 302},
    --concurrency = 10,
}

if not ok then
    ngx.log(ngx.ERR, "[health check]failed to spawn health checker: ", err)
    return
end
--ngx.log(ngx.ERR, "failed to spawn health checker: ", err)