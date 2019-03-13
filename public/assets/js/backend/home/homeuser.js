define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'home/homeuser/index',
                    add_url: 'home/homeuser/add',
                    edit_url: 'home/homeuser/edit',
                    del_url: 'home/homeuser/del',
                    multi_url: 'home/homeuser/multi',
                    table: 'home_user',
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
                        {field: 'username', title: __('Username')},
                        //{field: 'nickname', title: __('Nickname')},
                       // {field: 'password', title: __('Password')},
                        //{field: 'salt', title: __('Salt')},
                        {field: 'email', title: __('Email')},
                        {field: 'mobile', title: __('Mobile')},
                        {field: 'avatar', title: __('Avatar'),formatter: function(value,row,index){
                                var img='';
                                if(row.avatar!=''){
                                    img='<img src="'+row.avatar+'" width="30" height="25">';
                                }
                                
                                return img;
                        }},
                        {field: 'vocation', title: __('Vocation')},
                        {field: 'gender', title: __('Gender'),formatter: function(value,row,index){
                                var gender='';
                                if(row.gender==0){
                                    gender='未设置';
                                }else if(row.gender==1){
                                    gender='男';
                                }else if(row.gender==2){
                                    gender='女';
                                }
                                return gender;
                        }},
                        {field: 'hometown', title: __('Hometown'),formatter: function(value,row,index){
                            var hometown='';
                            var hometowns='';
                            if(row.hometown!=''){
                                eval('hometown='+row.hometown+'');
                                hometowns=hometown.province11+' - '+hometown.area11+' - '+hometown.city11;
                            }
                            return hometowns;

                        }},
                        {field: 'nhom', title: __('Nhom'),formatter: function(value,row,index){
                            var nhom='';
                            var nhoms='';
                            if(row.nhom!=''){
                                eval('nhom='+row.nhom+'');
                                nhoms=nhom.province22+' - '+nhom.area22+' - '+nhom.city22;
                            }
                            return nhoms;

                        }},
                        {field: 'domain', title: __('Domain')},
                        {field: 'phone', title: __('Phone')},
                        {field: 'qq', title: __('Qq')},
                        {field: 'wechat', title: __('Wechat')},
                        {field: 'popularity', title: __('Popularity')},
                        {field: 'fans', title: __('Fans')},
                        {field: 'release', title: __('Release')},
                        {field: 'bio', title: __('Bio')},
                        //{field: 'prevtime', title: __('Prevtime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        {field: 'logintime', title: __('Logintime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        {field: 'loginip', title: __('Loginip')},
                        //{field: 'loginfailure', title: __('Loginfailure')},
                        {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        //{field: 'updatetime', title: __('Updatetime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        //{field: 'token', title: __('Token')},
                        {field: 'status', title: __('Status'), formatter: Table.api.formatter.status},
                        //{field: 'verification', title: __('Verification')},
                        {field: 'user_type', title: '用户类型', searchList: {"0":__('User_type 0'),"1":__('User_type 1')}, formatter: Table.api.formatter.normal},
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