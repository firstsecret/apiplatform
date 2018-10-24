--
-- log handle
-- User: Bevan
-- Date: 2018/9/19
-- Time: 11:00
-- To change this template use File | Settings | File Templates.
--

-- send log
--local log_msg = ngx.ctx.log_msg
--if log_msg == nil then
--    log_msg = 'no message'
--end
--ngx.log(ngx.CRIT, ngx.var.resp_body)
--local error_status_code = ngx.ctx.err_status_code
--if error_status_code ~= nil then
--    ngx.log(ngx.ERR, log_msg)
--end

--ngx.log(ngx.ERR, "test log:")
local tool = require "resty.tool"
local redis = tool.getRedis()

-- slow log
if tonumber(ngx.var.upstream_response_time) >= 1 then
    ngx.log(ngx.WARN, "[SLOW] Ngx upstream response time: " .. ngx.var.upstream_response_time .. "s from " .. ngx.var.upstream_addr)
end

-- api count
local request_uri = ngx.var.request_uri
local remote_addr = ngx.var.remote_addr

local now_count = redis:get('api_count_' .. request_uri)
if now_count ~= ngx.null then
    local new_count = tonumber(now_count + 1)
    redis:set('api_count_' .. request_uri, new_count)
else
    redis:set('api_count_' .. request_uri, 1)
end

-- record ip every day request
local ip_request_count = redis:hget('ip_api_count_' .. remote_addr, request_uri)

if ip_request_count ~= ngx.null then
    redis:hset('ip_api_count_' .. remote_addr, request_uri, tonumber(ip_request_count + 1))
else
    redis:hset('ip_api_count_' .. remote_addr, request_uri, 1)
end
