--
-- horizon authorization
-- User: Bevan
-- Date: 2018/10/23
-- Time: 11:40
-- To change this template use File | Settings | File Templates.
--

-- ip check
local tool = require "resty.tool"

local redis = tool.getRedis()
local allow_horizon_ip = redis.get('allow_horizon_ip')
local remote_addr = ngx.var.remote_addr

if remote_addr == allow_horizon_ip then

else

end
