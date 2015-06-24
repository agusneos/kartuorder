<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');?>
<script type="text/javascript" src="<?=base_url('assets/easyui/datagrid-scrollview.js')?>"></script>

<!-- Data Grid -->
<table id="grid-report_get_all"
    data-options="pageSize:50, multiSort:true, remoteSort:true, rownumbers:true, singleSelect:true, 
                fit:true, fitColumns:true, toolbar:toolbar_report_get_all">
    <thead>
        <tr>
            <th data-options="field:'ck',checkbox:true" ></th>
            <th data-options="field:'tabel'"            width="70" halign="center" align="center" sortable="true">Tabel</th>
            <th data-options="field:'id'"               width="70" halign="center" align="center" sortable="true">No</th>
            <th data-options="field:'lot'"              width="100" align="center" sortable="true">Lot</th>
            <th data-options="field:'sub'"              width="70" halign="center" align="center" sortable="true">Sub Lot</th>
            <th data-options="field:'date'"             width="100" halign="center" align="center" sortable="true">Tanggal Barel</th>
            <th data-options="field:'upload'"           width="150" halign="center" align="center" sortable="true">Tanggal Capture</th>
            </tr>
    </thead>
</table>

<script type="text/javascript"> 
    var toolbar_report_get_all = [{
        text:'Image',
        iconCls:'icon-picture',
        handler:function(){transaksiBarelImage();}
    },{
        text:'Refresh',
        iconCls:'icon-reload',
        handler:function(){$('#grid-report_get_all').datagrid('reload');}
    }];
    
    $('#grid-report_get_all').datagrid({view:scrollview,remoteFilter:true,
        url:'<?php echo site_url('report/all/get_all'); ?>?grid=true'})
        .datagrid('enableFilter');
        
    function transaksiBarelImage(){
        var row = $('#grid-report_get_all').datagrid('getSelected');
        if (row)
        {
            $.post('<?php echo site_url('transaksi/checksheet/barel/viewImage'); ?>',{barel_id:row.barel_id},function(result){
                if (result.success){
                    var content = '<iframe scbarel="auto" frameborder="0"  src="'+result.img+'" style="width:100%;height:100%;"></iframe>';
                    var title   = row.barel_id;
                    if ($('#tt').tabs('exists', title))
                    {
                        $('#tt').tabs('select', title);
                        $('#dlg').dialog('close');
                    } 
                    else 
                    {
                        $('#tt').tabs('add',{
                            title:title,
                            content:content,
                            closable:true,
                            iconCls:'icon-picture'
                        });
                        $('#dlg').dialog('close');
                    }
                } else {
                    $.messager.show({
                        title: 'Error',
                        msg: 'Gambar Tidak Ditemukan'
                    });
                }
            },'json');
            
        }
        else
        {
            $.messager.alert('Info','Data belum dipilih !','info');
        }
    }
</script>

<!-- End of file v_get_all.php -->
<!-- Location: ./application/views/report/all/v_get_all.php -->