<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');?>
<script type="text/javascript" src="<?=base_url('assets/easyui/datagrid-scrollview.js')?>"></script>
<script type="text/javascript" src="<?=base_url('assets/easyui/datagrid-filter.js')?>"></script>
<script type="text/javascript">
    $.extend($.fn.datebox.defaults,{
        formatter:function(date){
            var y = date.getFullYear();
            var m = date.getMonth()+1;
            var d = date.getDate();
            return y+'-'+(m<10?('0'+m):m)+'-'+(d<10?('0'+d):d);
        },
        parser:function(s){
            if (!s) return new Date();
            var ss = (s.split('-'));
            var y = parseInt(ss[0],10);
            var m = parseInt(ss[1],10);
            var d = parseInt(ss[2],10);
            if (!isNaN(y) && !isNaN(m) && !isNaN(d)){
                return new Date(y,m-1,d);
            } else {
                return new Date();
            }
        }
    });
        
    $.extend($.fn.datetimebox.defaults,{
        formatter:function(date){
            var h = date.getHours();
            var M = date.getMinutes();
            var s = date.getSeconds();
            function formatNumber(value){
                return (value < 10 ? '0' : '') + value;
            }
            var separator = $(this).datetimebox('spinner').timespinner('options').separator;
            var r = $.fn.datebox.defaults.formatter(date) + ' ' + formatNumber(h)+separator+formatNumber(M);
            if ($(this).datetimebox('options').showSeconds){
                r += separator+formatNumber(s);
            }
            return r;
        },
        parser:function(s){
            if ($.trim(s) == ''){
                return new Date();
            }
            var dt = s.split(' ');
            var d = $.fn.datebox.defaults.parser(dt[0]);
            if (dt.length < 2){
                return d;
            }
            var separator = $(this).datetimebox('spinner').timespinner('options').separator;
            var tt = dt[1].split(separator);
            var hour = parseInt(tt[0], 10) || 0;
            var minute = parseInt(tt[1], 10) || 0;
            var second = parseInt(tt[2], 10) || 0;
            return new Date(d.getFullYear(), d.getMonth(), d.getDate(), hour, minute, second);
        }
    });
</script>

<!-- Data Grid -->
<table id="grid-admin_menu"
    data-options="pageSize:50, multiSort:true, remoteSort:true, rownumbers:true, singleSelect:true, 
                fit:true, fitColumns:true, toolbar:toolbar_admin_menu">
    <thead>
        <tr>
            <th data-options="field:'ck',checkbox:true" ></th>
            <th data-options="field:'a.id'"         width="30"  align="center"  sortable="true">ID</th>            
            <th data-options="field:'b.name'"       width="50"  align="center"  sortable="true">Parent Menu</th>
            <th data-options="field:'a.name'"       width="100" halign="center" sortable="true">Nama Menu</th>
            <th data-options="field:'a.uri'"        width="100" halign="center" sortable="true">URI</th>
            <th data-options="field:'a.allowed'"    width="100" halign="center" sortable="true">Allowed</th>
            <th data-options="field:'a.iconCls'"    width="100" halign="center" sortable="true">Icon</th>
            <th data-options="field:'a.type'"       width="30"  halign="center" sortable="true">Type</th>
        </tr>
    </thead>
</table>

<script type="text/javascript">
    var toolbar_admin_menu = [{
        id      : 'admin_menu-new',
        text    : 'New',
        iconCls : 'icon-new_file',
        handler : function(){adminMenuCreate();}
    },{
        id      : 'admin_menu-edit',
        text    : 'Edit',
        iconCls : 'icon-edit',
        handler : function(){adminMenuUpdate();}
    },{
        id      : 'admin_menu-delete',
        text    : 'Delete',
        iconCls : 'icon-cancel',
        handler : function(){adminMenuHapus();}
    },{
        text    : 'Refresh',
        iconCls : 'icon-reload',
        handler : function(){adminMenuRefresh();}
    },{
        id      : 'admin_menu-head_delete',
        text    : 'Delete/Edit Induk Menu',
        iconCls : 'icon-cancel',
        handler : function(){adminMenuHeadDelete();}
    }];
    
    function adminMenuRefresh() {
        $('#admin_menu-edit').linkbutton('disable');
        $('#admin_menu-delete').linkbutton('disable');
        $('#admin_menu-head_delete').linkbutton('disable');
        $('#grid-admin_menu').datagrid('reload');
    }
    
    function adminMenuHeadDelete() {
        $('#admin_menu-edit').linkbutton('enable');
        $('#admin_menu-delete').linkbutton('enable');
    }
    
    $('#grid-admin_menu').datagrid({view:scrollview,remoteFilter:true,
        url:'<?php echo site_url('admin/menu/index'); ?>?grid=true'})
        .datagrid({	
        onLoadSuccess: function(data){
            $('#admin_menu-edit').linkbutton('disable');
            $('#admin_menu-delete').linkbutton('disable');
            $('#admin_menu-head_delete').linkbutton('disable');
        },
        onClickRow: function(index,row){
            if(row['b.name'] === null){
                $('#admin_menu-edit').linkbutton('disable');
                $('#admin_menu-delete').linkbutton('disable');
                $('#admin_menu-head_delete').linkbutton('enable');
            }
            else{
                $('#admin_menu-edit').linkbutton('enable');
                $('#admin_menu-delete').linkbutton('enable');
                $('#admin_menu-head_delete').linkbutton('disable');
            }            
        },
        onDblClickRow: function(index,row){
            if(row['b.name'] !== null){
                adminMenuUpdate();
            }
	}
        }).datagrid('enableFilter');
        
    function adminMenuCreate(){
        $('#dlg-admin_menu').dialog({modal: true}).dialog('open').dialog('setTitle','Tambah Data');
        $('#fm-admin_menu').form('clear');
        url = '<?php echo site_url('admin/menu/create'); ?>';
        $('#induk_menu').combobox({
            url         : '<?php echo site_url('admin/menu/getParent'); ?>',
            valueField  : 'id',
            textField   : 'name'
        });
        $('#type_menu').combobox({
            url         : '<?php echo site_url('admin/menu/enumType'); ?>',
            valueField  : 'data',
            textField   : 'data'
        });
        //$('#induk_menu').combobox('reload', '<?php echo site_url('admin/menu/getParent'); ?>');
    }

function update_admin_menu(){
    var row = $('#grid-admin_menu').datagrid('getSelected');
    if(row){
        $('#dlg-admin_menu').dialog({modal: true}).dialog('open').dialog('setTitle','Edit Data');
        $('#fm-admin_menu').form('load',row);
        url = '<?php echo site_url('admin/menu/update'); ?>/' + row.id;
    }
    else
    {
         $.messager.alert('Info','Data belum dipilih !','info');
    }
}

function save_admin_menu(){
    $('#fm-admin_menu').form('submit',{
        url: url,
        onSubmit: function(){
            return $(this).form('validate');
        },
        success: function(result){
            var result = eval('('+result+')');
            if(result.success){
                $('#dlg-admin_menu').dialog('close');
                $('#grid-admin_menu').datagrid('reload');
                $.messager.show({
                    title: 'Info',
                    msg: 'Input/Ubah Data Berhasil'
                });
            } else {
                $.messager.show({
                    title: 'Error',
                    msg: 'Input/Ubah Data Gagal'
                });
            }
        }
    });
}

function delete_admin_menu(){
    var row = $('#grid-admin_menu').datagrid('getSelected');
    if (row){
        $.messager.confirm('Konfirmasi','Anda yakin ingin menghapus data ID '+row.id+' ?',function(r){
            if (r){
                $.post('<?php echo site_url('admin/menu/delete'); ?>',{id:row.id},function(result){
                    if (result.success){
                        $('#grid-admin_menu').datagrid('reload');
                        $.messager.show({
                            title: 'Info',
                            msg: 'Hapus Data Berhasil'
                        });
                    } else {
                        $.messager.show({
                            title: 'Error',
                            msg: 'Hapus Data Gagal'
                        });
                    }
                },'json');
            }
        });
    }
    else
    {
         $.messager.alert('Info','Data belum dipilih !','info');
    }
}


</script>
<style type="text/css">
    #fm-admin_menu{
        margin:0;
        padding:10px 30px;
    }
    .ftitle{
        font-size:14px;
        font-weight:bold;
        padding:5px 0;
        margin-bottom:10px;
        border-bottom:1px solid #ccc;
    }
    .fitem{
        margin-bottom:5px;
    }
    .fitem label{
        display:inline-block;
        width:80px;
    }
</style>

<!-- Dialog Form -->
<div id="dlg-admin_menu" class="easyui-dialog" style="width:400px; height:300px; padding: 10px 20px" closed="true" buttons="#dlg_buttons-admin_menu">
    <form id="fm-admin_menu" method="post" novalidate>
        <div class="fitem">
            <label for="type">Parent Menu</label>
            <input type=""text id="induk_menu" name="a.parentId" class="easyui-combobox" >
        </div>
        <div class="fitem">
            <label for="type">Nama Menu</label>
            <input type="text" name="a.name" class="easyui-textbox" required="true"/>
        </div>
        <div class="fitem">
            <label for="type">URI</label>
            <input type="text" name="a.uri" class="easyui-textbox" />
        </div>
        <div class="fitem">
            <label for="type">Allowed</label>
            <input type="text" name="a.allowed" class="easyui-textbox" required="true"/>
        </div>
        <div class="fitem">
            <label for="type">Icon</label>
            <input type="text" name="a.iconCls" class="easyui-textbox" />
        </div>
        <div class="fitem">
            <label for="type">Type</label>
            <input type=""text id="type_menu" class="easyui-combobox" name="a.type" />
        </div>
    </form>
</div>

<!-- Dialog Button -->
<div id="dlg_buttons-admin_menu">
    <a href="javascript:void(0)" class="easyui-linkbutton" data-options="width:75" iconCls="icon-ok" onclick="save_admin_menu()">Simpan</a>
    <a href="javascript:void(0)" class="easyui-linkbutton" data-options="width:75" iconCls="icon-cancel" onclick="javascript:$('#dlg-admin_menu').dialog('close')">Batal</a>
</div>

<!-- End of file v_menu.php -->
<!-- Location: ./application/views/admin/v_menu.php -->