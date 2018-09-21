--
-- jwt validate
-- User: Bevan
-- Date: 2018/9/21
-- Time: 14:26
-- To change this template use File | Settings | File Templates.
--

local cjson = require "cjson"
local jwt = require "resty.jwt"
local tool = require "resty.tool"

local key = tool.getJWTSecret()

--local jwt_token = "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9" ..
--        ".eyJmb28iOiJiYXIifQ" ..
--        ".VAoRL1IU0nOguxURF2ZcKR0SGKE1gCbqwyh8u2MLAyY"
local jwt_token = ngx.req.get_uri_args()['token']

if jwt_token == nil then
    -- header
    jwt_token = ngx.req.get_headers()['Authorization']
end

if jwt_token == nil then
    ngx.print(cjson.encode({ status_code = 4001, message = "未获取token" }))
    return
end

local jwt_obj = jwt:verify(key, jwt_token, {
    lifetime_grace_period = 0,
    require_exp_claim = true
})

--local jwt_obj = jwt:verify(key, jwt_token)

local response_table = {}
--ngx.say(jwt_obj['verified'])
if jwt_obj['verified'] then
    response_table['status_code'] = 0000
    response_table['message'] = '验证成功'
else
    response_table['status_code'] = 4005
    response_table['message'] = jwt_obj['reason']
end
ngx.print(cjson.encode(jwt_obj))
--ngx.print(cjson.encode(response_table))

