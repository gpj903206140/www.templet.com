define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'home/business/index',
                    add_url: 'home/business/add',
                    edit_url: 'home/business/edit',
                    del_url: 'home/business/del',
                    multi_url: 'home/business/multi',
                    table: 'business',
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
                        {field: 'user.username', title: '申请人'},
                        //{field: 'username', '申请人'},
                        {field: 'name', title: __('Name')},
                        {field: 'logo', title: __('Logo'),formatter: function(value,row,index){
                                var img='';
                                if(row.logo!=''){
                                    img='<img src="'+row.logo+'" width="30" height="25">';
                                }
                                
                                return img;
                        }},
                        {field: 'license_image', title: __('License_image'), formatter: Table.api.formatter.image},
                        {field: 'mobile', title: __('Mobile')},
                        {field: 'email', title: __('Email')},
                        {field: 'qq', title: __('Qq')},
                        {field: 'wechat', title: __('Wechat')},
                        {field: 'id_just', title: __('Id_just'),formatter: function(value,row,index){
                                var img='';
                                if(row.id_just!=''){
                                    img='<img src="'+row.id_just+'" width="30" height="25">';
                                }
                                
                                return img;
                        }},
                        {field: 'id_back', title: __('Id_back'),formatter: function(value,row,index){
                                var img='';
                                if(row.id_back!=''){
                                    img='<img src="'+row.id_back+'" width="30" height="25">';
                                }
                                
                                return img;
                        }},
                        {field: 'credit_code', title: __('Credit_code')},
                        {field: 'legal', title: __('Legal')},
                        {field: 'office', title: __('Office')},
                        {field: 'found_date', title: __('Found_date'), operate:'RANGE', addclass:'datetimerange'},
                        {field: 'enroll', title: __('Enroll')},
                        {field: 'enroll_address', title: __('Enroll_address')},
                        {field: 'main_products', title: __('Main_products')},
                        {field: 'company_type', title: __('Company_type'),formatter: function(value,row,index){
                            var val='';
                            var myjson="";
                            if(row.company_type!=''){
                                eval('myjson='+row.company_type+'');
                                for(var i=0;i<myjson.length;i++){
                                    val+=myjson[i]+'  ';
                                }
                            }
                            return val;

                        }},
                        {field: 'machining', title: __('Machining'),formatter: function(value,row,index){
                            var val='';
                            var myjson="";
                            if(row.machining!=''){
                                eval('myjson='+row.machining+'');
                                for(var i=0;i<myjson.length;i++){
                                    val+=myjson[i]+'  ';
                                }
                            }
                            return val;

                        }},
                        {field: 'device', title: __('Device'),formatter: function(value,row,index){
                            var val='';
                            var myjson="";
                            if(row.device!=''){
                                eval('myjson='+row.device+'');
                                for(var i=0;i<myjson.length;i++){
                                    val+=myjson[i]+'  ';
                                }
                            }
                            return val;

                        }},
                        {field: 'main_market', title: __('Main_market')},
                        {field: 'staff_num', title: __('Staff_num')},
                        {field: 'office_area', title: __('Office_area')},
                        {field: 'major_customers', title: __('Major_customers')},
                        {field: 'preference', title: __('Preference'),formatter: function(value,row,index){
                            var val='';
                            if(row.preference!=''){//alert(row.preference);
                                eval('preference='+row.preference+'');
                                //alert(preference[0]['name']);
                                for(var i=0;i<preference.length;i++){
                                    val+=preference[i]['name']+'  ';
                                }
                            }
                            return val;

                        }},
                        /*{field: 'trial', title: '企业认证',formatter: function(value,row,index){
                                var trial='';
                                if(row.trial==0){
                                    trial='待审核';
                                }else if(row.trial==1){
                                    trial='已通过';
                                }else if(row.trial==2){
                                    trial='已拒绝';
                                }
                                return trial;
                        }},*/
                        {field: 'addtime', title: __('Addtime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        {field: 'trial', title: '认证审核', formatter: function(value,row,index){
                                var trial='';
                                if(row.trial==0){
                                    trial='<a href="business/business_trial?trial=1&id='+row.id+'&user_id='+row.user_id+'" class="trial">通过</a>  <a href="business/business_trial?trial=2&id='+row.id+'&user_id='+row.user_id+'" class="trial">拒绝</a>';
                                    //trial='<a href="{:url(admin/home/business,array(trial=>1,id=>'+row.id+',user_id=>'+row.user_id+'))}" class="trial">通过</a>  <a href="{:url(admin/home/business,array(trial=>2,id=>'+row.id+',user_id=>'+row.user_id+'))}" class="trial">拒绝</a>';
                                }else if(row.trial==1){
    
                                    trial='已通过';
                                }else if(row.trial==2){
    
                                    trial='已拒绝';
                                }
                                return trial;
                        }}
                        //{field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
                    ]
                ]
            });

            // 为表格绑定事件
            Table.api.bindevent(table);
            var flag = false;
            $("body").on("click",".trial",function(){
                var url=this.href;
                if(flag){
                    return false;
                }
                flag = true;
                $.get(url,function(data){
                    eval('data='+data+'');
                    if(data.code==0){
                        layer.msg(data.msg);
                        //setTimeout(function(){window.location.href=document.referrer;}, 1000);
                        
                    }else if(data.code==1){
                        layer.msg(data.msg);
                        //setTimeout(function(){window.location.href=document.referrer;}, 1000);
                        setTimeout(function(){window.location.reload(0);}, 1000);
                        flag = false;
                    }else{
                        layer.msg(data.msg);
                        flag = false;
                    }
                })
                return false;
            })
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