--res = ngx.location.capture('/server')

--ngx.say(res.status)

--ngx.say(res.body)


local re_body = ngx.req.get_uri_args()

--for key, val in pairs(re_body) do
--    ngx.say(key .. ':', val)
--end
--re_body.insert(get_headers)
-- get headers

-- set header
-- http deal with
function httpHandler()
    local re_headers = ngx.req.get_headers()
    for key, val in pairs(re_headers) do
        --   ngx.say(key .. ':', val)
        ngx.req.set_header(key, val);
        --    ngx.req.set_header("Accept", "application/json");
    end
end

-- http error deal
function httpErrorHandler(err)
    print("Http headers deal error:", err)
end

status = xpcall(httpHandler, httpErrorHandler)
--print(status)
local request_uri = ngx.var.request_uri

--ngx.say(request_uri)
--ngx.print(headers)
local res = ngx.location.capture('/internal/api/testLua', { args = re_body })
ngx.say(res.status)
if res.status == 200 then
    ngx.print(res.body)
end
--ngx.say(res.status)

