define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'articleclass/index',
                    add_url: 'articleclass/add',
                    edit_url: 'articleclass/edit',
                    del_url: 'articleclass/del',
                    multi_url: 'articleclass/multi',
                    table: 'articleclass',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'class_id',
                sortName: 'class_id',
                columns: [
                    [
                        {checkbox: true},
                        {field: 'class_id', title: __('Class_id')},
                        {field: 'pid', title: __('Pid')},
                        {field: 'pname', title: __('Pname')},
                        {field: 'name', title: __('Name')},
                        {field: 'sort', title: __('Sort')},
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