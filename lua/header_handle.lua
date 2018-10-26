--
-- Response headers handle
-- User: Bevan
-- Date: 2018/10/15
-- Time: 16:21
-- To change this template use File | Settings | File Templates.
--

ngx.header['Server'] = 'xiaoyumi'
ngx.header['Charset'] = 'UTF-8'
--ngx.header['Content-Encoding'] = 'default'
local cjson = require "cjson"
local request_uri = ngx.var.uri
local r, e = ngx.re.match(request_uri, '/api/(?<name>.*)', 'jo')

local rn, re = ngx.re.match(r['name'],'^test','jo')

if r and r ~= ngx.null and (rn == nil or rn == ngx.null) then
    ngx.header['Content-Type'] = 'Application/json'
end
--
--elseif ngx.re.match(request_uri, '/(.*?).(gif|jpg|jpeg|png|bmp|swf|flv|mp4|ico|js|css)$') then
--    --    ngx.header['Content-Type'] = 'text/html'
--    --    ngx.header['test'] = '12'
--else
--    ngx.header['Content-Type'] = 'text/html'
--end