<?php
// +----------------------------------------------------------------------
// | Writen By lichunqiang
// +----------------------------------------------------------------------
// | Copyright (c) 2012-2014, All rights reserved.
// +----------------------------------------------------------------------
// | Author: Light <light-li@hotmail.com>
// +----------------------------------------------------------------------
namespace Light\Wechat\Interfaces;

interface ShopInterface
{
    const API_URL_PREFIX = 'https://api.weixin.qq.com/merchant';

    const PRODUCT_ADD = '/create?';
    const PRODUCT_DEL = '/del?';
    const PRODUCT_UPDATE = '/update?';
    const PRODUCT_GET = '/get?';
    const PRODUCT_GET_BY_STATUS = '/getbystatus?';
    const PRODUCT_MOD_STATUS = '/modproductstatus?';

    const CATE_GET_SUB = '/category/getsub?';
    const CATE_GET_SKU = '/category/getsku?';

    const STOCK_ADD = '/stock/add?';
    const STOCK_REDUCE = '/stock/reduce?';

    const EXPRESS_ADD = '/express/add?';
    const EXPRESS_DEL = '/express/del?';
    const EXPRESS_UPDATE = '/express/update?';
    const EXPRESS_GET_BYID = '/express/getbyid?';
    const EXPRESS_GET_ALL = '/express/getall?';

    const GROUP_ADD = '/group/add?';
    const GROUP_DEL = '/group/del?';
    const GROUP_UPDATE = '/group/update?';
    const GROUP_UPDATE_ATTR = '/group/propertymod?';
    const GROUP_UPDATE_MOD = '/group/productmod?';
    const GROUP_GET_ALL = '/group/getall?';
    const GROUP_GET_BYID = '/group/getbyid?';

    const SHELF_ADD = '/shelf/add?';
    const SHELF_DEL = '/shelf/del?';
    const SHELF_UPDATE = '/shelf/mod?';
    const SHELF_GET_ALL = '/shelf/getall?';
    const SHELF_GET_BYID = '/shelf/getbyid?';

    const ORDER_GET_BYID = '/order/getbyid?';
    const ORDER_GET_BYFILTER = '/order/getbyfilter?';
    const ORDER_SET_DELIVERY = '/order/setdelivery?';
    const ORDER_CLOSE = '/order/close?';

    const UPLOAD_IMG = '/common/upload_img?';

    //---------------------------商品管理

    /**
     * 增加商品
     * POST数据格式：json
     *
     * @param array $data 商品属性数据结构
     * @return mixed
     */
    function createProduct($data);

    /**
     * 删除商品
     * POST数据格式：json
     *
     * @param string $product_id 商品ID
     * @return mixed
     */
    function deleteProduct($product_id);

    /**
     * 更新商品
     * 从未上架的商品所有信息均可改，否则商品名称、商品分类、商品属性这三个字段不可改
     * POST数据格式：json
     *
     * @param array $data 更新商品的数据
     */
    function updateProduct($data);

    /**
     * 查询商品
     * 请求方式: GET
     *
     * @param string $product_id 查询的商品ID
     * @return mixed
     */
    function getProduct($product_id);

    /**
     * 获取指定状态的所有商品
     *
     * @param int $status 商品状态(0-全部, 1-上架, 2-下架), 默认为0
     * @return mixed
     */
    function getProductByStatus($status = 0);

    /**
     * 商品上下架
     *
     * @param string $product_id 商品的ID
     * @param int $status 商品上下架标识(0-下架, 1-上架)
     */
    function updateProductStatus($product_id, $status);

    /**
     * 获取指定分类的所有子分类
     *
     * @param int $cate_id 大分类ID(根节点分类id为1)
     * @return mixed
     */
    function getCategorySubList($cate_id);

    /**
     * 获取指定子分类的所有SKU
     *
     * @param int $cate_id 分类ID
     * @return mixed
     */
    function getCategorySku($cate_id);

    /**
     * 获取指定分类的所有属性
     *
     * @param int $cate_id 分类ID
     * @return mixed
     */
    function getCategoryAttr($cate_id);

    //------------------------库存管理

    /**
     * 增加库存
     * POST数据格式：json
     *
     * @param string $product_id 商品ID
     * @param string $sku_info sku信息，格式：'id1:vid1;id2:vid2'
     * @param int quantity 增加的库存数量
     * @return mixed
     */
    function addStock($product_id, $sku_info, $quantity);

    /**
     * 减少库存
     * POST数据格式：json
     *
     * @param string $product_id 商品ID
     * @param string $sku_info sku信息，格式：'id1:vid1;id2:vid2'
     * @param int quantity 减少的库存数量
     * @return mixed
     */
    function reduceStock($product_id, $sku_info, $quantity);

    //--------------------邮费模板管理

    /**
     * 增加邮费模板
     * POST数据格式：json
     *
     * @param array $data 模板信息
     * @return mixed
     */
    function addExpressTemplate($data);

    /**
     * 删除邮费模板
     * POST数据格式：json
     *
     * @param string $template_id 邮费模板ID
     * @return mixed
     */
    function deleteExpressTemplate($template_id);

    /**
     * 修改邮费模板
     * POST数据格式：json
     *
     * @param string $template_id  邮费模板ID
     * @param array $template_info 邮费模板信息
     * @return mixed
     */
    function updateExpressTemplate($template_id, $template_info);

    /**
     * 获取指定ID的邮费模板
     * POST数据格式：json
     *
     * @param string $template_id  邮费模板ID
     * @return mixed
     */
    function getExpressTemplateById($template_id);

    /**
     * 获取所有邮费模板
     *
     * @return mixed
     */
    function getExpressTemplateList();

    //------------分组管理

    /**
     * 增加分组,将商品加入特定的分组
     * POST数据格式：json
     *
     * @param array $data 商品分组信息
     * @return mixed
     */
    function setProductGroup($data);

    /**
     * 删除分组
     * POST数据格式：json
     *
     * @param int $group_id 分组ID
     * @return mixed
     */
    function delProductGroup($group_id);

    /**
     * 修改分组属性
     * POST数据格式：json
     *
     * @param int $group_id 分组ID
     * @param string $group_name 修改后的分组名称
     * @return mixed
     */
    function updateProdctGroup($group_id, $group_name);

    /**
     * 修改分组商品
     * POST数据格式：json
     *
     * @example $product = array('product_id' => 'xxx', 'mod_action' => 1)
     *             product_id 商品ID，mod_action 修改操作(0-删除 1-增加)
     *
     * @param int $group_id 分组ID
     * @param array $product 分组的商品合集
     * @return mixed
     */
    function updateProductGroupMod($product_id, $product);

    /**
     * 获取所有分组(GET)
     *
     * @return mixed
     */
    function getProductGroupList();

    /**
     * 根据分组ID获取分组信息
     * POST数据格式：json
     *
     * @param int $group_id 分组ID
     * @return mixed
     */
    function getProductGroupById($group_id);

    //----------------货架管理

    /**
     * 增加货架
     * POST数据格式：json
     *
     * @param array $data 货架详情信息
     * @return mixed
     */
    function addShelf($data);

    /**
     * 删除货架
     * POST数据格式：json
     *
     * @param int $shelf_id 货架ID(增加货架时返回)
     * @return mixed
     */
    function deleteShelf($shelf_id);

    /**
     * 修改货架
     * POST数据格式：json
     *
     * @param array $data 修改货架的信息数据
     * @return mixed
     */
    function updateShelf($data);

    /**
     * 获取所有货架(GET)
     *
     * @return mixed
     */
    function getShelfList();

    /**
     * 根据货架ID获取货架信息
     *
     * @param int $shelf_id 货架ID
     * @return mixed
     */
    function getShelfById($shelf_id);

    //--------------------订单管理
    //用户微信付款成功后会将订单信息推送到设置的回调URL

    /**
     * 根据订单ID获取订单详情
     *
     * @param int $order_id 订单ID
     * @return mixed
     */
    function getOrderById($order_id);

    /**
     * 根据订单状态/创建时间获取订单详情
     *
     * @param int $status 订单状态 (不带该字段-全部状态, 2-待发货, 3-已发货, 5-已完成, 8-维权中)
     * @param int $begin_time 订单创建时间起始时间(不带该字段则不按照时间做筛选)
     * @param int $end_time 订单创建时间终止时间(不带该字段则不按照时间做筛选)
     * @return mixed
     */
    function filterOrder($status, $begin_time, $end_time);

    /**
     * 设置订单发货信息
     *
     * @param array $data 发货信息数据结构
     * @return mixed
     */
    function setOrderDelivery($data);

    /**
     * 关闭订单
     *
     * @param int $order_id 订单ID
     * @return mixed
     */
    function closeOrder($order_id);

    //-------------功能接口

    /**
     * 上传图片
     *
     * @param string $file_path 图片所在路径
     * @return mixed
     */
    function uploadImage($file_path);

}
