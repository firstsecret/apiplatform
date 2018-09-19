local args = ngx.req.get_uri_args()
local vars = ngx.var
local headers = ngx.req.get_headers()

if (headers) then
    for k, val in pairs(headers) do
        ngx.say(k .. ':', val)
    end
end

if (args) then
    for key, val in pairs(args) do
        ngx.say(key .. ':', val)
    end
end

if (vars) then
    for key, val in pairs(vars) do
        ngx.say(key, val)
    end
end
