-- require tool
local tool = require('resty.tool')
local zhttp = require("resty.http")
local redis_client = require('resty.redis_client')
local redis = redis_client:new()

--local json = require "cjson";
--local string = require('resty.string')

-- body
ngx.req.read_body();

-- set header
-- http handle
local capture_headers = {}
function httpHandler()
    local re_headers = ngx.req.get_headers()
    for key, val in pairs(re_headers) do
        --        ngx.say(key)
        capture_headers[key] = val
        ngx.req.set_header(key, val);
    end
end

-- gzip handle
ngx.req.set_header('Accept-Encoding', 'default')
capture_headers['Accept-Encoding'] = 'default'
-- http error deal
function httpErrorHandler(err)
    --    print("Http headers deal error:", err)
    tool.respClient(4070, 'http头信息格式不正确')
end

status = xpcall(httpHandler, httpErrorHandler)

local request_uri = ngx.var.request_uri
local request_method = ngx.var.request_method

-- add real ip
capture_headers['X-Forwarded-For'] = ngx.var.remote_addr
-- http module
local httpc = zhttp.new()


-- 获取 服务 请求 列表
-- 根据服务端的 心跳包 确定 服务是否 已挂

-- post args handle
local post_args = ngx.req.get_post_args()
local post_str = ''
for k, v in pairs(post_args) do
    post_str = post_str .. k .. '=' .. v .. '&'
end

post_str = string.sub(post_str, 1, string.len(post_str) - 1)

local apiplatform_service_base_uri = redis:exec(function(red) return red:get('apiplatform_service_base_uri') end)

if apiplatform_service_base_uri == ngx.null or apiplatform_service_base_uri == nil or err then
    tool.respClient(5123,'服务提供已关闭')
end

local request_base_uri = 'http://' .. apiplatform_service_base_uri


local timeout = timeout or 5000
httpc:set_timeout(timeout)
local body = ngx.req.read_body();
local res, err_ = httpc:request_uri(request_base_uri, {
    path = request_uri,
    method = request_method,
    body = body,
    headers = capture_headers,
})
-- error handle
if not res then
    ngx.log(ngx.CRIT, 'http request service error:' .. err_)
    tool.respClient(5103, '服务异常' .. err_)
else
    ngx.print(res.body)
end
