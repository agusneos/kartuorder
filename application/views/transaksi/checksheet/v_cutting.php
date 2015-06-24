<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');?>
<script type="text/javascript" src="<?=base_url('assets/easyui/datagrid-scrollview.js')?>"></script>
<script type="text/javascript" src="<?=base_url('assets/easyui/datagrid-filter.js')?>"></script>
<script type="text/javascript" src="<?=base_url('assets/webcamjs/webcam.js')?>"></script>
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
<table id="grid-transaksi_cutting"
    data-options="pageSize:50, multiSort:true, remoteSort:true, rownumbers:true, singleSelect:true, 
                fit:true, fitColumns:true, toolbar:toolbar_transaksi_cutting">
    <thead>
        <tr>
            <th data-options="field:'ck',checkbox:true" ></th>
            <th data-options="field:'cutting_id'"                 width="70" halign="center" align="center" sortable="true">No</th>
            <th data-options="field:'cutting_lot'"                   width="100" align="center" sortable="true">Lot</th>
            <th data-options="field:'cutting_sub'"                 width="70" halign="center" align="center" sortable="true">Sub Lot</th>
            <th data-options="field:'cust_name'"                 width="200" halign="center" align="left" sortable="true">Nama Customer</th>
            <th data-options="field:'item_name'"                 width="200" halign="center" align="left" sortable="true">Nama Barang</th>
            <th data-options="field:'cutting_date'"                 width="100" halign="center" align="center" sortable="true">Tanggal Cutting</th>
            <th data-options="field:'cutting_upload'"                 width="150" halign="center" align="center" sortable="true">Tanggal Capture</th>
            </tr>
    </thead>
</table>

<script type="text/javascript"> 
    var toolbar_transaksi_cutting = [{
        text:'New',
        id:'baruCutting',
        iconCls:'icon-new_file',
        handler:function(){transaksiCuttingCreate();}
    },{
        text:'Edit',
        iconCls:'icon-edit',
        handler:function(){transaksiCuttingUpdate();}
    },{
        text:'Delete',
        iconCls:'icon-cancel',
        handler:function(){transaksiCuttingHapus();}
    },{
        text:'Session Date',
        iconCls:'icon-date',
        handler:function(){transaksiCuttingSesdate();}
    },{
        text:'Image',
        iconCls:'icon-picture',
        handler:function(){transaksiCuttingImage();}
    },{
        text:'Refresh',
        iconCls:'icon-reload',
        handler:function(){$('#grid-transaksi_cutting').datagrid('reload');}
    },{
        text:'Update Tgl. Cutting After',
        iconCls:'icon-date',
        handler:function(){transaksiCuttingAfter();}
    },{
        text:'Update Tgl. Cutting Between',
        iconCls:'icon-date',
        handler:function(){transaksiCuttingBetween();}
    },{
        text:'Cek Total Entry Data',
        iconCls:'icon-date',
        handler:function(){transaksiCuttingCheck();}
    }];
    
    $('#grid-transaksi_cutting').datagrid({view:scrollview,remoteFilter:true,
        url:'<?php echo site_url('transaksi/checksheet/cutting/index'); ?>?grid=true'})
        .datagrid('enableFilter');
        
    $(function(){	
        $('#baruCutting').focus(); // Saat awal membuka menu lagsung fokus ke tombol new
    });
    
    function transaksiCuttingCreate() {
        $('#dlg-transaksi_cutting').dialog({modal: true, closable: false}).dialog('open').dialog('setTitle','Tambah Data');
        $('#fm-transaksi_cutting').form('clear');
        //$('#cutting_oksave').blur();
        url = '<?php echo site_url('transaksi/checksheet/cutting/create'); ?>';
        
        $.post('<?php echo site_url('transaksi/checksheet/cutting/getDateCutting'); ?>',function(result){
            $('#cutting_date').datebox('setValue', result.sesdate);
            },'json'); //ambil session date

        Webcam.set({
            width: 430,
            height: 240,
            dest_width: 1280,
            dest_height: 720,
            force_flash: true,
            image_format: 'jpeg',
            jpeg_quality: 90
        });
        Webcam.setSWFLocation('assets/webcamjs/webcam.swf');
        Webcam.attach('#webcamCutting');
        
        $('#captureCutting').linkbutton({
            iconCls: '',
            text:'Capture'
        });
        $('#captureCutting').focus();
        $('#captureCutting').bind('click', function(){
            $('#captureCutting').linkbutton({
                iconCls: 'icon-ok',
                text:'OK'
            });            
            $('#cutting_lot').textbox('setValue', '');
            $('#cutting_customer').textbox('setValue', '');
            $('#cutting_barang').textbox('setValue', '');
            $('#cutting_sub').textbox('setValue', '');
            $('#cutting_lot').next().find('input').focus();
        });
       
        $('#cutting_lot').textbox('textbox').keypress(function(e){
            if (e.which === 13){                
               $('#chlotCutting').focus();
            }
        });
        
        $('#chlotCutting').bind('click', function(){
            $('#cutting_sub').next().find('input').focus();
        });
        
        $('#cutting_sub').textbox('textbox').keypress(function(e){
            if (e.which === 13){
                    $('#cutting_oksave').focus();
            }
        });
    }
    
    function captureCutting() {
        Webcam.snap( function(data_uri) {
            $('#imgCutting').textbox('setValue',data_uri);
        });
    }
    
    function checkLotCutting() {
        var lotidCutting = $('#cutting_lot').textbox('getValue');
        $.post('<?php echo site_url('transaksi/checksheet/cutting/cekLot'); ?>',{cutting_lot:lotidCutting},function(result){
            if (result.success){
                $('#cutting_sub').next().find('input').focus();
                $.post('<?php echo site_url('transaksi/checksheet/cutting/getCustItem'); ?>',{cutting_lot:lotidCutting},function(result){
                    $('#cutting_customer').textbox('setValue', result.customer);
                    $('#cutting_barang').textbox('setValue', result.barang);
                },'json');
            } else {
                $('#cutting_lot').textbox('setValue', '');
                $('#cutting_customer').textbox('setValue', '');
                $('#cutting_barang').textbox('setValue', '');
                $('#cutting_sub').textbox('setValue', '');
                $('#cutting_lot').next().find('input').focus();
                $.messager.show({
                    title: 'Error',
                    msg: 'Lot Tidak Ditemukan'
                });
            }
        },'json');
    }
    
    function transaksiCuttingUpdate() {
        var row = $('#grid-transaksi_cutting').datagrid('getSelected');
        if(row){
            $('#dlg-transaksi_cutting-edit').dialog({modal: true}).dialog('open').dialog('setTitle','Edit Data');
            $('#fm-transaksi_cutting-edit').form('load',row);
            url = '<?php echo site_url('transaksi/checksheet/cutting/update'); ?>/' + row.cutting_id;            
        }
        else
        {
             $.messager.alert('Info','Data belum dipilih !','info');
        }
    }
    
    function transaksiCuttingSave(){
        $('#fm-transaksi_cutting').form('submit',{
            url: url,
            onSubmit: function(){
                return $(this).form('validate');
            },
            success: function(result){
                var result = eval('('+result+')');
                if(result.success){
                    $('#dlg-transaksi_cutting').dialog('close');
                    //$('#grid-transaksi_cutting').datagrid('reload');
                    transaksiCuttingCreate();
                    $.messager.show({
                        title: 'Info',
                        msg: 'Input Data Berhasil'
                    });
                } else {
                    $.messager.show({
                        title: 'Error',
                        msg: 'Input Data Gagal'
                    });
                }
            }
        });
    }
    
    function transaksiCuttingSaveEdit(){
        $('#fm-transaksi_cutting-edit').form('submit',{
            url: url,
            onSubmit: function(){
                return $(this).form('validate');
            },
            success: function(result){
                var result = eval('('+result+')');
                if(result.success){
                    $('#dlg-transaksi_cutting-edit').dialog('close');
                    $('#grid-transaksi_cutting').datagrid('reload');
                    $.messager.show({
                        title: 'Info',
                        msg: 'Ubah Data Berhasil'
                    });
                } else {
                    $.messager.show({
                        title: 'Error',
                        msg: 'Ubah Data Gagal'
                    });
                }
            }
        });
    }
    
    function transaksiCuttingHapus(){
        var row = $('#grid-transaksi_cutting').datagrid('getSelected');
        if (row){
            $.messager.confirm('Konfirmasi','Anda yakin ingin menghapus Cutting '+row.cutting_id+' ?',function(r){
                if (r){
                    $.post('<?php echo site_url('transaksi/checksheet/cutting/delete'); ?>',{cutting_id:row.cutting_id},function(result){
                        if (result.success){
                            $('#grid-transaksi_cutting').datagrid('reload');
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
    
    function transaksiCuttingImage(){
        var row = $('#grid-transaksi_cutting').datagrid('getSelected');
        if (row)
        {
            $.post('<?php echo site_url('transaksi/checksheet/cutting/viewImage'); ?>',{cutting_id:row.cutting_id},function(result){
                if (result.success){
                    var content = '<iframe sccutting="auto" frameborder="0"  src="'+result.img+'" style="width:100%;height:100%;"></iframe>';
                    var title   = row.cutting_id;
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

    function transaksiCuttingSesdate()
    {
        $('#dlg-transaksi_cutting_sesdate').dialog({modal: true}).dialog('open').dialog('setTitle','Ubah Session Date');
        $('#fm-transaksi_cutting_sesdate').form('clear');
        $.post('<?php echo site_url('transaksi/checksheet/cutting/getDateCutting'); ?>',function(result){
            $('#sesdate').datebox('setValue', result.sesdate);
            },'json');
    }
    
    function transaksiCuttingSesdateSave()
    {
        var sesdate = $('#sesdate').datebox('getValue');
        $.post('<?php echo site_url('transaksi/checksheet/cutting/updateSesdate'); ?>',{sesdate:sesdate},function(result){
            if(result.success)
            {
                $('#dlg-transaksi_cutting_sesdate').dialog('close');
                $.messager.show({
                    title: 'Info',
                    msg: 'Ubah Data Berhasil'
                });
            }
            else
            {
                $.messager.show({
                    title: 'Error',
                    msg: 'Ubah Data Gagal'
                });
            }
        },'json');
    }
    
    function transaksiCuttingAfter()
    {
        $('#dlg-transaksi_cutting-after').dialog({modal: true, closable: false}).dialog('open').dialog('setTitle','Update Tgl. Cutting After');
        $('#fm-transaksi_cutting-after').form('clear');
        url = '<?php echo site_url('transaksi/checksheet/cutting/updateAfter'); ?>';
    }
    
    function transaksiCuttingAfterSave()
    {
        $('#fm-transaksi_cutting-after').form('submit',{
            url: url,
            onSubmit: function(){
                return $(this).form('validate');
            },
            success: function(result){
                var result = eval('('+result+')');
                if(result.success){
                    $('#grid-transaksi_cutting').datagrid('reload');
                    $('#dlg-transaksi_cutting-after').dialog('close');
                    $.messager.show({
                        title: 'Info',
                        msg: 'Ubah Data Berhasil'
                    });
                } else {
                    $.messager.show({
                        title: 'Error',
                        msg: 'Ubah Data Gagal'
                    });
                }
            }
        });
    }
    
    function transaksiCuttingBetween()
    {
        $('#dlg-transaksi_cutting-between').dialog({modal: true, closable: false}).dialog('open').dialog('setTitle','Update Tgl. Cutting Between');
        $('#fm-transaksi_cutting-between').form('clear');
        url = '<?php echo site_url('transaksi/checksheet/cutting/updateBetween'); ?>';
    }
    
    function transaksiCuttingBetweenSave()
    {
        $('#fm-transaksi_cutting-between').form('submit',{
            url: url,
            onSubmit: function(){
                return $(this).form('validate');
            },
            success: function(result){
                var result = eval('('+result+')');
                if(result.success){
                    $('#grid-transaksi_cutting').datagrid('reload');
                    $('#dlg-transaksi_cutting-between').dialog('close');
                    $.messager.show({
                        title: 'Info',
                        msg: 'Ubah Data Berhasil'
                    });
                } else {
                    $.messager.show({
                        title: 'Error',
                        msg: 'Ubah Data Gagal'
                    });
                }
            }
        });
    }
    
    function transaksiCuttingCheck()
    {
        $('#dlg-transaksi_cutting-check').dialog({modal: true, closable: false}).dialog('open').dialog('setTitle','Check Total Entry Data per Tanggal');
        $('#fm-transaksi_cutting-check').form('clear');
        url = '<?php echo site_url('transaksi/checksheet/cutting/check'); ?>';
    }
    
    function transaksiCuttingCheckSave()
    {
        $('#fm-transaksi_cutting-check').form('submit',{
            url: url,
            onSubmit: function(){
                return $(this).form('validate');
            },
            success: function(result){
                var result = eval('('+result+')');
                if(result.success)
                {
                    $.messager.show({
                        title: 'Info',
                        msg: 'Total Entry Data Tanggal '+result.tgl+' Sebanyak '+result.total+' Data'
                    }); 
                }                               
            }
        });
    }
    
</script>
<style type="text/css">
    #fm-transaksi_cutting{
        margin:0;
        padding:10px 30px;
    }
    #fm-transaksi_cutting-edit{
        margin:0;
        padding:10px 30px;
    }
    #fm-transaksi_cutting-after{
        margin:0;
        padding:10px 30px;
    }
    #fm-transaksi_cutting-between{
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
        width:100px;
    }
    .fitem input{
        display:inline-block;
        width:150px;
    }
    .center a{
        margin-left: 200px;
        margin-top: 5px;
        margin-bottom: 10px;
    }
    .grup2{
        margin-left: 75px;
    }

</style>

<div id="dlg-transaksi_cutting" class="easyui-dialog" style="width:650px; height:600px; padding: 10px 20px" closed="true" buttons="#dlg-buttons-transaksi_cutting">
    <form id="fm-transaksi_cutting" method="post" novalidate> 
        <div class="easyui-panel" data-options="style:{margin:'1% auto'}" style="position:relative;overflow:hidden;width:427px;height:240px">
            <div id="webcamCutting">
            </div>
        </div>
        <div class="center">
            <a id="captureCutting" href="javascript:void(0)" class="easyui-linkbutton" data-options="width:75" iconCls="icon-ok" onclick="captureCutting()">Capture</a>
        </div>
        <div class="grup2">
            <div class="fitem">
                <label for="type"></label>
                <input id="imgCutting" name="img" class="easyui-textbox" data-options="readonly: true"/>
            </div>
            <div class="fitem">
                <label for="type">No Lot</label>
                <input id="cutting_lot" name="cutting_lot" class="easyui-textbox" required="true" tabindex="1"/>
                <a id="chlotCutting" href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-search" plain="true" onclick="checkLotCutting()"></a>
            </div>
            <div class="fitem">
                <label for="type">Sub Lot</label>
                <input id="cutting_sub" name="cutting_sub" class="easyui-textbox" required="true" tabindex="2"/>
            </div>
            <div class="fitem">
                <label for="type">Customer</label>
                <input id="cutting_customer" name="cutting_customer" style="width: 300px" class="easyui-textbox" data-options="readonly: true"/>
            </div>
            <div class="fitem">
                <label for="type">Item</label>
                <input id="cutting_barang" name="cutting_barang" style="width: 300px" class="easyui-textbox" data-options="readonly: true"/>
            </div>
            <div class="fitem">
                <label for="type">Tanggal Cutting</label>
                <input id="cutting_date" name="cutting_date" class="easyui-datebox" required="true"/>
            </div>
        </div>
    </form>
</div>

<!-- Dialog Button -->
<div id="dlg-buttons-transaksi_cutting">
    <a href="javascript:void(0)" id="cutting_oksave" class="easyui-linkbutton c1" data-options="width:75" iconCls="icon-save" tabindex="3" onclick="transaksiCuttingSave()">Simpan</a>
    <a href="javascript:void(0)" class="easyui-linkbutton c5" data-options="width:75" iconCls="icon-cancel" onclick="javascript:$('#dlg-transaksi_cutting').dialog('close');Webcam.reset();$('#grid-transaksi_cutting').datagrid('reload');">Batal</a>
</div>

<div id="dlg-transaksi_cutting-edit" class="easyui-dialog" style="width:400px; height:300px; padding: 10px 20px" closed="true" buttons="#dlg-buttons-transaksi_cutting-edit">
    <form id="fm-transaksi_cutting-edit" method="post" novalidate>        
        <div class="fitem">
            <label for="type">No Lot</label>
            <input type="text" id="cutting_lot" name="cutting_lot" class="easyui-textbox" required="true"/>
        </div>
        <div class="fitem">
            <label for="type">Sub Lot</label>
            <input type="text" id="cutting_sub" name="cutting_sub" class="easyui-textbox" required="true"/>
        </div>
        <div class="fitem">
            <label for="type">Tanggal Cutting</label>
            <input type="text" id="cutting_date" name="cutting_date" class="easyui-datebox" required="true"/>
        </div>
    </form>
</div>

<!-- Dialog Button -->
<div id="dlg-buttons-transaksi_cutting-edit">
    <a href="javascript:void(0)" class="easyui-linkbutton" data-options="width:75" iconCls="icon-ok" onclick="transaksiCuttingSaveEdit()">Simpan</a>
    <a href="javascript:void(0)" class="easyui-linkbutton" data-options="width:75" iconCls="icon-cancel" onclick="javascript:$('#dlg-transaksi_cutting-edit').dialog('close')">Batal</a>
</div>

<div id="dlg-transaksi_cutting_sesdate" class="easyui-dialog" style="width:400px; height:200px; padding: 10px 20px" closed="true" buttons="#dlg-buttons-transaksi_cutting_sesdate">
    <form id="fm-transaksi_cutting_sesdate" method="post" novalidate>
        <div class="fitem">
            <label for="type">Session Date</label>
            <input id="sesdate" name="sesdate" class="easyui-datebox" required="true"/>
        </div>
    </form>
</div>

<!-- Dialog Button -->
<div id="dlg-buttons-transaksi_cutting_sesdate">
    <a href="javascript:void(0)" class="easyui-linkbutton" data-options="width:75" iconCls="icon-ok" onclick="transaksiCuttingSesdateSave()">Simpan</a>
    <a href="javascript:void(0)" class="easyui-linkbutton" data-options="width:75" iconCls="icon-cancel" onclick="javascript:$('#dlg-transaksi_cutting_sesdate').dialog('close')">Batal</a>
</div>

<div id="dlg-transaksi_cutting-after" class="easyui-dialog" style="width:400px; height:300px; padding: 10px 20px" closed="true" buttons="#dlg-buttons-transaksi_cutting-after">
    <form id="fm-transaksi_cutting-after" method="post" novalidate>        
        <div class="fitem">
            <label for="type">Tanggal Cutting</label>
            <input type="text" id="aa" name="aa" class="easyui-datebox" required="true"/>
        </div>
        <div class="fitem">
            <label for="type">Setelah Input</label>
            <input type="text" id="bb" name="bb" class="easyui-datetimebox" required="true"/>
        </div>
        <div class="fitem">
            <label for="type">Update Ke Tanggal</label>
            <input type="text" id="cc" name="cc" class="easyui-datebox" required="true"/>
        </div>
    </form>
</div>

<!-- Dialog Button -->
<div id="dlg-buttons-transaksi_cutting-after">
    <a href="javascript:void(0)" class="easyui-linkbutton" data-options="width:75" iconCls="icon-ok" onclick="transaksiCuttingAfterSave()">Simpan</a>
    <a href="javascript:void(0)" class="easyui-linkbutton" data-options="width:75" iconCls="icon-cancel" onclick="javascript:$('#dlg-transaksi_cutting-after').dialog('close')">Batal</a>
</div>

<div id="dlg-transaksi_cutting-between" class="easyui-dialog" style="width:400px; height:300px; padding: 10px 20px" closed="true" buttons="#dlg-buttons-transaksi_cutting-between">
    <form id="fm-transaksi_cutting-between" method="post" novalidate>        
        <div class="fitem">
            <label for="type">Tanggal Cutting</label>
            <input type="text" id="dd" name="dd" class="easyui-datebox" required="true"/>
        </div>
        <div class="fitem">
            <label for="type">Setelah Input</label>
            <input type="text" id="ee" name="ee" class="easyui-datetimebox" required="true"/>
        </div>
        <div class="fitem">
            <label for="type">Sebelum Input</label>
            <input type="text" id="ff" name="ff" class="easyui-datetimebox" required="true"/>
        </div>
        <div class="fitem">
            <label for="type">Update Ke Tanggal</label>
            <input type="text" id="gg" name="gg" class="easyui-datebox" required="true"/>
        </div>
    </form>
</div>

<!-- Dialog Button -->
<div id="dlg-buttons-transaksi_cutting-between">
    <a href="javascript:void(0)" class="easyui-linkbutton" data-options="width:75" iconCls="icon-ok" onclick="transaksiCuttingBetweenSave()">Simpan</a>
    <a href="javascript:void(0)" class="easyui-linkbutton" data-options="width:75" iconCls="icon-cancel" onclick="javascript:$('#dlg-transaksi_cutting-between').dialog('close')">Batal</a>
</div>


<div id="dlg-transaksi_cutting-check" class="easyui-dialog" style="width:400px; height:200px; padding: 10px 20px" closed="true" buttons="#dlg-buttons-transaksi_cutting-check">
    <form id="fm-transaksi_cutting-check" method="post" novalidate>        
        <div class="fitem">
            <label for="type">Tanggal Cutting</label>
            <input type="text" id="check_date" name="check_date" class="easyui-datebox" required="true"/>
        </div>
    </form>
</div>

<!-- Dialog Button -->
<div id="dlg-buttons-transaksi_cutting-check">
    <a href="javascript:void(0)" class="easyui-linkbutton" data-options="width:75" iconCls="icon-ok" onclick="transaksiCuttingCheckSave()">Check</a>
    <a href="javascript:void(0)" class="easyui-linkbutton" data-options="width:75" iconCls="icon-cancel" onclick="javascript:$('#dlg-transaksi_cutting-check').dialog('close')">Batal</a>
</div>
<!-- End of file v_cutting.php -->
<!-- Location: ./application/views/transaksi/v_cutting.php -->