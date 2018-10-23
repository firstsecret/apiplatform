--
-- 40x error html.
-- User: Bevan
-- Date: 2018/9/18
-- Time: 18:19
-- To change this template use File | Settings | File Templates.
--
local tool = require('resty.tool')
local status = ngx.status

-- http status table list
local status_table = {}
status_table[300] = 'SPECIAL_RESPONSE'
status_table[301] = 'MOVED_PERMANENTLY'
status_table[302] = 'MOVED_TEMPORARILY'
status_table[303] = 'SEE_OTHER'
status_table[304] = 'NOT_MODIFIED'
status_table[400] = 'BAD_REQUEST '
status_table[401] = 'UNAUTHORIZED'
status_table[403] = 'FORBIDDEN'
status_table[404] = 'NOT_FOUND'
status_table[405] = 'NOT_ALLOWED'
status_table[444] = 'No Response'
status_table[500] = 'INTERNAL_SERVER_ERROR'
status_table[501] = 'METHOD_NOT_IMPLEMENTED'
status_table[503] = 'SERVICE_UNAVAILABLE'
status_table[504] = 'GATEWAY_TIMEOUT'
--ngx.say(ngx.var.uri)
local status_name = status_table[status]
if status_name == nil then
    status_name = 'NOT_FOUND'
    status = 404
end

-- response rewrite

--tool.rewriteResponse('Server', 'xiaoyumi')
--ngx.say('is  now')
ngx.print('<head><title>' .. status .. '  ' .. status_name .. ' </title></head><body bgcolor="white"><center><h1>' .. status .. '  ' .. status_name .. ' </h1></center><hr><center>xiaoyumi<center></body>')


