FORMAT: 1A

# Example

# Auth
Class AuthController

## api开放平台接入用户登录api [POST /cli/login]
登录api,支持电话/邮箱/账号 登录

+ Request (application/x-www-form-urlencoded)
    + Body

            {'login_name':'login_name', 'password':'password'}

+ Response 200 (application/json)
    + Body

            {
                "status_code": 200,
                "message": "success",
                "reqData": {
                    "access_token": "token",
                    "express_in": 7200
                }
            }

## api开放平台接入用户注册api [POST /cli/register]
注册api, 等待邮箱 短信 激活功能接入

+ Request (application/x-www-form-urlencoded)
    + Body

            {'name':'account','email':'email','password':'password'}

+ Response 200 (application/json)
    + Body

            {
                "status_code": 200,
                "message": "success",
                "reqData": {
                    "access_token": "token",
                    "express_in": 7200
                }
            }

# AppHttpApiV1ApiAuthController
api授权
Class ApiAuthController

## 获取已授权的个人信息 [GET /cli/getUserInfo]
携带 header Authorization

+ Request (application/x-www-form-urlencoded)
    + Headers

            Authorization: token

+ Response 200 (application/json)
    + Body

            {
                "status_code": 200,
                "message": "success",
                "resqData": {
                    "user_name": "user_name",
                    "user_email": "user_email"
                }
            }

## api授权 [GET /cli/token]
api授权 携带params  ?app_key=&app_secret=

+ Request (application/x-www-form-urlencoded)
    + Headers

            Authorization: token
    + Body

            app_key=appkey&app_secret=appsecret

+ Response 200 (application/json)
    + Body

            {
                "status_code": 200,
                "message": "success",
                "resqData": {
                    "access_token": "token",
                    "express_in": 7200
                }
            }

## 主动刷新token [GET /cli/refreshAccessToken]
携带header头 主动请求刷新

+ Request (application/x-www-form-urlencoded)
    + Headers

            Authorization: token

+ Response 200 (application/json)
    + Body

            {
                "status_code": 200,
                "message": "success",
                "resqData": {
                    "access_token": "token",
                    "express_in": 7200
                }
            }

# PlatformProduct
Class PlatformProductController

## api平台可以接入产品列表 [GET /cli/productList/{?type}]
可按类型 默认全部 分页

+ Request (application/x-www-form-urlencoded)

+ Response 200 (application/json)
    + Body

            {
                "status_code": 200,
                "message": "success",
                "reqData": {
                    "list": "list"
                }
            }

## api平台可接入产品分类列表 [GET /cli/categoriesList]
按分类排序 归类完毕 便于前端展示

+ Request (application/x-www-form-urlencoded)

+ Response 200 (application/json)
    + Body

            {
                "status_code": 200,
                "message": "success",
                "reqData": {
                    "list": "list"
                }
            }

# ProductService
Class ProductServiceController

## api平台接入用户更新开通的服务 [POST /cli/editServiceSelf]
api平台接入用户更新开通的服务

+ Request (application/x-www-form-urlencoded)
    + Headers

            Authorization: token
    + Body

            {'product_ids':'product_ids'}

+ Response 200 (application/json)
    + Body

            {
                "status_code": 200,
                "message": "成功",
                "respData": ""
            }

## api平台接入用户开通服务 [POST /cli/openServiceSelf/{product_id}]
api平台接入用户开通服务

+ Request (application/x-www-form-urlencoded)
    + Headers

            Authorization: token
    + Body

            {'product_id':'product_id'}

+ Response 200 (application/json)
    + Body

            {
                "status_code": 200,
                "message": "成功",
                "respData": ""
            }

## api平台接入用户关闭服务 [POST /cli/delService/{product_id}]
api平台接入用户关闭服务

+ Request (application/x-www-form-urlencoded)
    + Headers

            Authorization: token
    + Body

            {'product_id':'product_id'}

+ Response 200 (application/json)
    + Body

            {
                "status_code": 200,
                "message": "成功",
                "respData": ""
            }

# AdminPlatformProduct
Class PlatformProductController

## 添加产品服务api [POST /cli/admin/platformProduct]
添加产品服务api (admin/operator角色)

+ Request (application/x-www-form-urlencoded)
    + Headers

            Authorization: token
    + Body

            {'category_id':'category_id', 'name': 'name', 'detail': 'detail'}

+ Response 200 (application/json)
    + Body

            {
                "status_code": 200,
                "message": "success",
                "respData": ""
            }

## 编辑产品服务api [PUT /cli/admin/platformProduct/{product_id}]
编辑产品服务api (admin/operator角色)

+ Request (application/x-www-form-urlencoded)
    + Headers

            Authorization: token
    + Body

            {'product_id':'product_id', 'name': 'name', 'detail': 'detail'}

+ Response 200 (application/json)
    + Body

            {
                "status_code": 200,
                "message": "success",
                "respData": ""
            }

## 下架某一个产品服务api [DELETE /cli/admin/platformProduct/{product_id}]
下架产品服务 (admin/operator角色)

+ Request (application/x-www-form-urlencoded)
    + Headers

            Authorization: token
    + Body

            {'product_id':'product_id'}

+ Response 200 (application/json)
    + Body

            {
                "status_code": 200,
                "message": "success",
                "respData": ""
            }

# AdminAuth
Class AuthController

## 内部应用获取授权api [GET /cli/admin/token]
内部应用获取授权

+ Request (application/x-www-form-urlencoded)
    + Headers

            Authorization: token
    + Body

            app_key=appkey&app_secret=appsecret

+ Response 200 (application/json)
    + Body

            {
                "status_code": 200,
                "message": "success",
                "resqData": {
                    "access_token": "token",
                    "express_in": 7200
                }
            }

## 添加一个内部应用授权api [POST /cli/admin/createNewInternal]
添加一个内部应用授权 （admins/operator角色可调用）

+ Request (application/x-www-form-urlencoded)
    + Headers

            Authorization: token
    + Body

            app_key=appkey&app_secret=appsecret

+ Response 200 (application/json)
    + Body

            {
                "status_code": 200,
                "message": "success",
                "resqData": {
                    "app_key": "app_key",
                    "app_secret": "app_secret",
                    "uuid": "uuid"
                }
            }

# AdminLogin
Class LoginController

## 后台管理系统登录api [POST /cli/admin/login]
后台管理系统登录 ,admin与operator可登录

+ Request (application/x-www-form-urlencoded)
    + Body

            {'login_name':'login_name', 'password': 'password'}

+ Response 200 (application/json)
    + Body

            {
                "status_code": 200,
                "message": "success",
                "respData": {
                    "access_token": "token",
                    "express_in": 7200
                }
            }

# Internal/Internal
Class InternalController

## 开通新的用户 [POST /cli/admin/openUser]
开通新的用户 返回openid , 如果 满足 内部uuid机制 已存在将返回 对应的 openid 并 提示， 没有则 新增该用户 并生成openid 返回

+ Request (application/x-www-form-urlencoded)
    + Headers

            Authorization: token
    + Body

            {'sign':'sign','appKey':'appKey','sequenceId':'sequenceId','reqData':{'name':'name','telephone':'telephone','password':'password','type':'type'}}

+ Response 200 (application/json)
    + Body

            {
                "status_code": 200,
                "message": "success",
                "respData": {
                    "openid": "openid"
                }
            }

# InternalPlatformProduct
Class PlatformProductController

## 内部应用开放接口-- 给某一用户开通某个服务 [POST /cli/admin/openUserService]
内部应用开放接口-- 给某一用户开通某个服务

+ Request (application/x-www-form-urlencoded)
    + Headers

            Authorization: token
    + Body

            {'reqData':{'product_ids':'product_ids', 'openid': 'openid'},'sign':'sign','appKey':'appKey','sequenceId':'sequenceId'}

+ Response 200 (application/json)
    + Body

            {
                "status_code": 200,
                "message": "success",
                "respData": ""
            }

## 禁用某用户的应用服务api [POST /cli/admin/disableUserService]
禁用某用户的应用服务api

+ Request (application/x-www-form-urlencoded)
    + Headers

            Authorization: token
    + Body

            {'sign':'sign','appKey':'appKey','sequenceId':'sequenceId','reqData':{'product_ids':'product_ids', 'openid': 'openid'}}

+ Response 200 (application/json)
    + Body

            {
                "status_code": 200,
                "message": "success",
                "respData": ""
            }