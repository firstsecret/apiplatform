--
-- log handle
-- User: Bevan
-- Date: 2018/9/19
-- Time: 11:00
-- To change this template use File | Settings | File Templates.
--

-- log
local log_msg = ngx.ctx.log_msg
--if log_msg == nil then
--    log_msg = 'no message'
--end

local error_status_code = ngx.ctx.err_status_code
--if error_status_code ~= nil then
ngx.log(ngx.ERR, 'self message:')
--end
