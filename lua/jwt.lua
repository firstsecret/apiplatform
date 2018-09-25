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
local express_in = 0

if app_key == nil or app_secret == nil then
    local resp_table = { status_code = 4011, message = "appkey或appsecret缺失" }
    tool.setNgxVar('resp_body', tool.serialize(resp_table))
    ngx.print(cjson.encode(resp_table))
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

local real_app_secret
local app_key_type = 0

if not ok then
    ngx.log(ngx.CRIT, "Redis Connect error while retrieving ip_blacklist: " .. err)
    local resp_table = { status_code = 5009, message = "服务出错,请联系管理人员进行服务恢复" }
    tool.setNgxVar('resp_body', tool.serialize(resp_table))
    ngx.print(cjson.encode(resp_table))
    return
else
    local r_app_secret = red:get(app_key)
    --    ngx.print(r_app_secret)
    if r_app_secret == nil then
        ngx.print(cjson.encode({ status_code = 4009, message = "appkey不存在" }))
        return
    else
        real_app_secret = string.sub(r_app_secret, 1, 32)
        local tmp_key_type = string.sub(r_app_secret, 33, 33)
        app_key_type = tonumber(tmp_key_type)

        --        app_key_type = string.sub(r_app_secret, 33, 33)
        if (real_app_secret ~= app_secret) then
            ngx.print(cjson.encode({ status_code = 4008, message = "appsecret不正确" }))
            return
        end
    end
end

--
local key = tool.getJWTSecret()
--
local request_uri = ngx.var.request_uri

local jwt_token
--local app_key_type_number = tonumber(app_key_type)

if app_key_type == 1 then
    -- forever
    --    ngx.say('is 1')
    jwt_token = jwt:sign(key,
        {
            header = { typ = "JWT", alg = "HS256" },
            payload = {}
        })

elseif app_key_type == 0 then
    --    ngx.say('is 0')
    local host_port = ngx.var.server_port
    local host = ngx.var.host

    local http_addr = host .. ':' .. host_port .. request_uri

    local iat = math.ceil(ngx.now())

    express_in = 7200

    local exp = iat + express_in
    local nbf = iat
    local jti = random.bytes(6)

    jwt_token = jwt:sign(key,
        {
            header = { typ = "JWT", alg = "HS256" },
            payload = { iss = http_addr, iat = iat, exp = exp, nbf = nbf, jti = jti }
        })
end

-- local jwt_token = jwt:sign(key,
-- {
-- header = { typ = "JWT", alg = "HS256" },
-- payload = {}
-- })
--
--ngx.print(jwt_token)
ngx.print(cjson.encode({ status_code = 200, message = "success", data = { token = jwt_token, express_in = express_in } }))