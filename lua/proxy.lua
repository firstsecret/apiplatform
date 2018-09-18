-- body
ngx.req.read_body();

local re_args = ngx.req.get_uri_args()
--re_args.insert(get_headers)
-- get headers

-- set header
-- http handle
function httpHandler()
    local re_headers = ngx.req.get_headers()
    for key, val in pairs(re_headers) do
        --        ngx.say(key)
        ngx.req.set_header(key, val);
    end
end

-- gzip handle
ngx.req.set_header('Accept-Encoding', 'default')

-- http error deal
function httpErrorHandler(err)
    print("Http headers deal error:", err)
end

status = xpcall(httpHandler, httpErrorHandler)
--print(status)
local request_uri = ngx.var.request_uri
local request_method = ngx.var.request_method
--ngx.say(request_method)
--ngx.say(request_uri)
--ngx.print(headers)
local res = {}
if (request_method == 'POST') then
    res = ngx.location.capture('/internal/' .. request_uri, { method = ngx.HTTP_POST, args = re_args })
elseif (request_method == 'GET') then
    res = ngx.location.capture('/internal/' .. request_uri, { method = ngx.HTTP_GET, args = re_args })
elseif (request_method == 'PUT') then
    res = ngx.location.capture('/internal/' .. request_uri, { method = ngx.HTTP_PUT, args = re_args })
elseif (request_method == 'DELETE') then
    res = ngx.location.capture('/internal/' .. request_uri, { method = ngx.HTTP_DELETE, args = re_args })
elseif (request_method == 'OPTIONS') then
    res = ngx.location.capture('/internal/' .. request_uri, { method = ngx.HTTP_OPTIONS, args = re_args })
end
--ngx.say(request_uri)
--ngx.req.set_header("Content-Type", "application/json;charset=utf8");
--ngx.req.set_header("Accept", "application/json");
--get response header
for k, v in pairs(res.header) do
    if k ~= "Transfer-Encoding" and k ~= "Connection" then
        ngx.header[k] = v
    end
end

-- response handle
ngx.header['Server'] = 'xiaoyumi'

-- response
ngx.print(res.body)

-- log
file = io.open("/tmp/capture.log", "a+")
file:write(res.body)
for k, v in pairs(res.header) do
    file:write(k .. ':' .. v)
end
file:close()
--local res = ngx.location.capture('/testInternal', {args = re_args})
--ngx.say(res.status)

--if res.status == 200 then
--    ngx.print(res.body)
--end)

