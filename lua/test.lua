--
-- Created by IntelliJ IDEA.
-- User: Admin
-- Date: 2018/10/15
-- Time: 16:41
-- To change this template use File | Settings | File Templates.
--

local request_uri = ngx.var.request_uri
local regex = [[/api/(.*)?]]
local m = ngx.re.match(request_uri, '/api/(.*)?', "jo")

if m then
    ngx.print(m[0])
else
    ngx.print('未匹配')
end


