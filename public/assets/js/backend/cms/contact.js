define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'cms/contact/index',
                    add_url: 'cms/contact/add',
                    edit_url: 'cms/contact/edit',
                    del_url: 'cms/contact/del',
                    multi_url: 'cms/contact/multi',
                    table: 'contact',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'id',
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id')},
                        {field: 'title', title: __('Title')},
                        {field: 'qq', title: __('Qq')},
                        {field: 'email', title: __('Email')},
                        {field: 'address', title: __('Address')},
                        {field: 'wx', title: __('Wx')},
                        //{field: 'wx_code', title: __('Wx_code')},
                        {field: 'image', title: '微信二唯码图', formatter: Table.api.formatter.image},
                        {field: 'address_jw', title: __('Address_jw')},
                        {field: 'sort', title: __('Sort')},
                        {field: 'addtime', title: __('Addtime'), operate:'RANGE', addclass:'datetimerange'},
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
                    ]
                ]
            });

            // 为表格绑定事件
            Table.api.bindevent(table);
        },
        add: function () {
            Controller.api.bindevent();
        },
        edit: function () {
            Controller.api.bindevent();
        },
        api: {
            bindevent: function () {
                Form.api.bindevent($("form[role=form]"));
            }
        }
    };
    return Controller;
});