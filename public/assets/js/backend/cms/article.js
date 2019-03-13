define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'cms/article/index',
                    add_url: 'cms/article/add',
                    edit_url: 'cms/article/edit',
                    del_url: 'cms/article/del',
                    multi_url: 'cms/article/multi',
                    table: 'article',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'article_id',
                sortName: 'article_id',
                columns: [
                    [
                        {checkbox: true},
                        {field: 'article_id', title: __('Article_id')},
                        {field: 'title', title: __('Title')},
                        {field: 'class_id', title: __('Class_id')},
                        {field: 'class_name', title: __('Class_name')},
                        //{field: 'image', title: __('Image'), formatter: Table.api.formatter.image},
                        {field: 'sort', title: __('Sort')},
                        {field: 'create_time', title: __('Create_time'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        {field: 'modify_time', title: __('Modify_time'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
                    ]
                ]
            });

            // 为表格绑定事件
            Table.api.bindevent(table);
        },
        add: function () {
            Controller.api.changepname();
            $(document).on('change','#c-class_id',function(){
                Controller.api.changepname();
            })
            Controller.api.bindevent();
        },
        edit: function () {
            Controller.api.changepname();
            $(document).on('change','#c-class_id',function(){
                Controller.api.changepname();
            })
            Controller.api.bindevent();
        },
        api: {
            bindevent: function () {
                Form.api.bindevent($("form[role=form]"));
            },
            changepname:function(){
                var a = $('#c-class_id').find('option:selected').text();
                $('#c-class_name').val(a);
            }        
        }
    };
    return Controller;
});