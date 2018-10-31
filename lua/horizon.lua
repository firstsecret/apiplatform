--
-- horizon authorization
-- User: Bevan
-- Date: 2018/10/23
-- Time: 11:40
-- To change this template use File | Settings | File Templates.
--

-- ip check
local redis_client = require "resty.redis_client"
local redis = redis_client:new()
local allow_horizon_ip = redis:exec(function(red) return red.get('allow_horizon_ip') end)
local remote_addr = ngx.var.remote_addr

if remote_addr == allow_horizon_ip then

else
end
