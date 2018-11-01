--
-- Created by IntelliJ IDEA.
-- User: Admin
-- Date: 2018/10/15
-- Time: 16:41
-- To change this template use File | Settings | File Templates.
--

--local request_uri = ngx.var.request_uri
--local regex = [[/api/(.*)?]]
--local m = ngx.re.match(request_uri, '/api/(.*)?', "jo")
--
--if m then
--    ngx.print(m[0])
--else
--    ngx.print('未匹配')
--end


local tool = require "resty.tool"
local cjson = require "cjson"
local redis = require "resty.redis_client"

--
--
--ngx.print(cjson.encode(res))
local app_key = 'api_app_key:439d8c975f26e5005dcdbf41b0d84161'
local red = redis.new()
local res, err = red:exec(function(red)
    local info = red:hgetall(app_key)
    local rt = {}

    for i = 1, #info, 2 do
        rt[info[i]] = info[i + 1]
    end
    --            ngx.print(cjson.encode(rt))
    return rt
end)

ngx.print(cjson.encode(res))