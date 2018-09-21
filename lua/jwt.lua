--
-- jwt sign
-- User: Bevan
-- Date: 2018/9/21
-- Time: 13:49
-- To change this template use File | Settings | File Templates.
--

local cjson = require "cjson"
local jwt = require "resty.jwt"
local tool = require "resty.tool"
local random = require "resty.random"


-- check user
local args = ngx.req.get_uri_args()

local app_key = args['app_key']
local app_secret = args['app_secret']

if app_key == nil or app_secret == nil then
    ngx.print(cjson.encode({ status_code = 4011, message = "appkey或appsecret缺失" }))
    return
end

-- redis
local redis_host = '127.0.0.1'
local redis_port = '6379'

-- don't too long
local redis_connection_timeout = 100

local redis = require "resty.redis"
local red = redis:new()

red:set_timeout(redis_connection_timeout)

local ok, err = red:connect(redis_host, redis_port)

if not ok then
    ngx.log(ngx.CRIT, "Redis Connect error while retrieving ip_blacklist: " .. err)
    ngx.print(cjson.encode({ status_code = 5009, message = "服务出错,请联系管理人员进行服务恢复" }))
    return
else
    local r_app_secret = red:get(app_key)

    if r_app_secret ~= app_secret then
        ngx.print(cjson.encode({status_code = 4010, message = "appkey不存在或者appsecret不正确"}))
        return
    end
end

--
--local key = tool.getJWTSecret()
--
--local request_uri = ngx.var.request_uri
----ngx.say(var)
----ngx.say(type(var))
--local host_port = ngx.var.server_port
--local host = ngx.var.host
--
--local http_addr = host .. ':' .. host_port .. request_uri
--
--local iat = math.ceil(ngx.now())
--
--local exp = iat + 60 * 60
--local nbf = iat
--local jti = random.bytes(6)
--
--local jwt_token = jwt:sign(key,
--    {
--        header = { typ = "JWT", alg = "HS256" },
--        payload = { iss = http_addr, iat = iat, exp = exp, nbf = nbf, jti = jti }
--    })
--
----local jwt_token = jwt:sign(key,
----    {
----        header = { typ = "JWT", alg = "HS256" },
----        payload = {}
----    })
--
--ngx.print(jwt_token)