define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'cms/articleclass/index',
                    add_url: 'cms/articleclass/add',
                    edit_url: 'cms/articleclass/edit',
                    del_url: 'cms/articleclass/del',
                    multi_url: 'cms/articleclass/multi',
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
                        //{field: 'pid', title: __('Pid')},
                        {field: 'pname', title: '上级名称'/*,formatter:function(i,row,v){
                            return row.pname +'www.baidu.com';
                        }*/},
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
            Controller.api.changepname();
            $(document).on('change','#c-pid',function(){
                Controller.api.changepname();
            })
            Controller.api.bindevent();
        },
        edit: function () {
            Controller.api.changepname();
            $(document).on('change','#c-pid',function(){
                Controller.api.changepname();
            })
            Controller.api.bindevent();
        },
        api: {
            bindevent: function () {
                Form.api.bindevent($("form[role=form]"));
            },
            changepname:function(){
                var a = $('#c-pid').find('option:selected').text();
                $('#c-pname').val(a);
            }
        }
    };
    return Controller;
});