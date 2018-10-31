-- require tool
local tool = require('resty.tool')
--local cjson = require "cjson";
--local string = require('resty.string')

-- body
ngx.req.read_body();

local re_args = ngx.req.get_uri_args()

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
    --    print("Http headers deal error:", err)
    tool.respClient(4070, 'http头信息格式不正确')
end

status = xpcall(httpHandler, httpErrorHandler)

local request_uri = ngx.var.request_uri
local request_method = ngx.var.request_method

-- add real ip
ngx.req.set_header('X-Forwarded-For', ngx.var.remote_addr)

-- capture method
local capture_method = ngx['HTTP_' .. request_method]

local res = ngx.location.capture('/internal' .. request_uri, {method = capture_method, args = re_args})

for k, v in pairs(res.header) do
    if k ~= "Transfer-Encoding" and k ~= "Connection" then
        ngx.header[k] = v
    end
end

if res.status == 200 then
    ngx.print(res.body)
else
--    ngx.print(cjson.encode(res))
    tool.respClient(res.status, 'Err request')
end

-- response



