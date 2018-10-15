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

local request_uri = ngx.var.request_uri

if ngx.re.match(request_uri, '/api/(.*?)', 'jo') then
    ngx.header['Content-Type'] = 'Application/json'
end
--
--elseif ngx.re.match(request_uri, '/(.*?).(gif|jpg|jpeg|png|bmp|swf|flv|mp4|ico|js|css)$') then
--    --    ngx.header['Content-Type'] = 'text/html'
--    --    ngx.header['test'] = '12'
--else
--    ngx.header['Content-Type'] = 'text/html'
--end