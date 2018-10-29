-- require tool
local tool = require('resty.tool')
local zhttp = require("resty.http")
local cjson = require "cjson";
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
--ngx.say(request_method)
--ngx.print(json.encode(ngx.var.remote_addr))

-- add real ip
ngx.req.set_header('X-Forwarded-For', ngx.var.remote_addr)

-- http module
local httpc = zhttp.new()

-- redis
local redis = tool.getRedis()

-- 获取 服务 请求 列表
-- 根据服务端的 心跳包 确定 服务是否 已挂


-- post args handle
--local post_args = ngx.req.get_post_args()
--local post_str = ''
--for k, v in pairs(post_args) do
--    post_str = post_str .. k .. '=' .. v .. '&'
--end
--string.len(post_str)
--local len = string.len(post_str) - 1
--ngx.print(len)
--post_str = string.sub(post_str, 1, string.len(post_str) - 1)

--local url = request_base_uri .. request_uri
--ngx.say(request_uri)

-- capture method
local capture_method = ngx['HTTP_' .. request_method]

local res = ngx.location.capture('/internal/' .. request_uri, {method = capture_method, args = re_args})

--ngx.say(request_uri)
--ngx.req.set_header("Content-Type", "application/json;charset=utf8");
--ngx.req.set_header("Accept", "application/json");
--get response header
for k, v in pairs(res.header) do
    if k ~= "Transfer-Encoding" and k ~= "Connection" then
        ngx.header[k] = v
    end
end

if res.status == 200 then
    ngx.print(res.body)
else
    ngx.print(cjson.encode(res))
--    tool.respClient(res.status, 'Err request')
end

-- response handle
--tool.rewriteResponse('RequestUri', request_uri)
-- ctx
--ngx.ctx.log_msg = res.body

-- response



