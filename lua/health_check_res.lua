--
-- get health check result
-- User: bevan
-- Date: 2018/10/18
-- Time: 17:53
-- To change this template use File | Settings | File Templates.
--

local hc = require "resty.upstream.healthcheck"
ngx.say("Nginx Worker PID: ", ngx.worker.pid())
ngx.print(hc.status_page())
