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
<table id="grid-transaksi_turret"
    data-options="pageSize:50, multiSort:true, remoteSort:true, rownumbers:true, singleSelect:true, 
                fit:true, fitColumns:true, toolbar:toolbar_transaksi_turret">
    <thead>
        <tr>
            <th data-options="field:'ck',checkbox:true" ></th>
            <th data-options="field:'turret_id'"                 width="70" halign="center" align="center" sortable="true">No</th>
            <th data-options="field:'turret_lot'"                   width="100" align="center" sortable="true">Lot</th>
            <th data-options="field:'turret_sub'"                 width="70" halign="center" align="center" sortable="true">Sub Lot</th>
            <th data-options="field:'cust_name'"                 width="200" halign="center" align="left" sortable="true">Nama Customer</th>
            <th data-options="field:'item_name'"                 width="200" halign="center" align="left" sortable="true">Nama Barang</th>
            <th data-options="field:'turret_date'"                 width="100" halign="center" align="center" sortable="true">Tanggal Turret</th>
            <th data-options="field:'turret_upload'"                 width="150" halign="center" align="center" sortable="true">Tanggal Capture</th>
            </tr>
    </thead>
</table>

<script type="text/javascript"> 
    var toolbar_transaksi_turret = [{
        text:'New',
        id:'baruTurret',
        iconCls:'icon-new_file',
        handler:function(){transaksiTurretCreate();}
    },{
        text:'Edit',
        iconCls:'icon-edit',
        handler:function(){transaksiTurretUpdate();}
    },{
        text:'Delete',
        iconCls:'icon-cancel',
        handler:function(){transaksiTurretHapus();}
    },{
        text:'Session Date',
        iconCls:'icon-date',
        handler:function(){transaksiTurretSesdate();}
    },{
        text:'Image',
        iconCls:'icon-picture',
        handler:function(){transaksiTurretImage();}
    },{
        text:'Refresh',
        iconCls:'icon-reload',
        handler:function(){$('#grid-transaksi_turret').datagrid('reload');}
    },{
        text:'Update Tgl. Turret After',
        iconCls:'icon-date',
        handler:function(){transaksiTurretAfter();}
    },{
        text:'Update Tgl. Turret Between',
        iconCls:'icon-date',
        handler:function(){transaksiTurretBetween();}
    },{
        text:'Cek Total Entry Data',
        iconCls:'icon-date',
        handler:function(){transaksiTurretCheck();}
    }];
    
    $('#grid-transaksi_turret').datagrid({view:scrollview,remoteFilter:true,
        url:'<?php echo site_url('transaksi/checksheet/turret/index'); ?>?grid=true'})
        .datagrid('enableFilter');
        
    $(function(){	
        $('#baruTurret').focus(); // Saat awal membuka menu lagsung fokus ke tombol new
    });
    
    function transaksiTurretCreate() {
        $('#dlg-transaksi_turret').dialog({modal: true, closable: false}).dialog('open').dialog('setTitle','Tambah Data');
        $('#fm-transaksi_turret').form('clear');
        //$('#turret_oksave').blur();
        url = '<?php echo site_url('transaksi/checksheet/turret/create'); ?>';
        
        $.post('<?php echo site_url('transaksi/checksheet/turret/getDateTurret'); ?>',function(result){
            $('#turret_date').datebox('setValue', result.sesdate);
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
        Webcam.attach('#webcamTurret');
        
        $('#captureTurret').linkbutton({
            iconCls: '',
            text:'Capture'
        });
        $('#captureTurret').focus();
        $('#captureTurret').bind('click', function(){
            $('#captureTurret').linkbutton({
                iconCls: 'icon-ok',
                text:'OK'
            });            
            $('#turret_lot').textbox('setValue', '');
            $('#turret_customer').textbox('setValue', '');
            $('#turret_barang').textbox('setValue', '');
            $('#turret_sub').textbox('setValue', '');
            $('#turret_lot').next().find('input').focus();
        });
       
        $('#turret_lot').textbox('textbox').keypress(function(e){
            if (e.which === 13){                
               $('#chlotTurret').focus();
            }
        });
        
        $('#chlotTurret').bind('click', function(){
            $('#turret_sub').next().find('input').focus();
        });
        
        $('#turret_sub').textbox('textbox').keypress(function(e){
            if (e.which === 13){
                    $('#turret_oksave').focus();
            }
        });
    }
    
    function captureTurret() {
        Webcam.snap( function(data_uri) {
            $('#imgTurret').textbox('setValue',data_uri);
        });
    }
    
    function checkLotTurret() {
        var lotidTurret = $('#turret_lot').textbox('getValue');
        $.post('<?php echo site_url('transaksi/checksheet/turret/cekLot'); ?>',{turret_lot:lotidTurret},function(result){
            if (result.success){
                $('#turret_sub').next().find('input').focus();
                $.post('<?php echo site_url('transaksi/checksheet/turret/getCustItem'); ?>',{turret_lot:lotidTurret},function(result){
                    $('#turret_customer').textbox('setValue', result.customer);
                    $('#turret_barang').textbox('setValue', result.barang);
                },'json');
            } else {
                $('#turret_lot').textbox('setValue', '');
                $('#turret_customer').textbox('setValue', '');
                $('#turret_barang').textbox('setValue', '');
                $('#turret_sub').textbox('setValue', '');
                $('#turret_lot').next().find('input').focus();
                $.messager.show({
                    title: 'Error',
                    msg: 'Lot Tidak Ditemukan'
                });
            }
        },'json');
    }
    
    function transaksiTurretUpdate() {
        var row = $('#grid-transaksi_turret').datagrid('getSelected');
        if(row){
            $('#dlg-transaksi_turret-edit').dialog({modal: true}).dialog('open').dialog('setTitle','Edit Data');
            $('#fm-transaksi_turret-edit').form('load',row);
            url = '<?php echo site_url('transaksi/checksheet/turret/update'); ?>/' + row.turret_id;            
        }
        else
        {
             $.messager.alert('Info','Data belum dipilih !','info');
        }
    }
    
    function transaksiTurretSave(){
        $('#fm-transaksi_turret').form('submit',{
            url: url,
            onSubmit: function(){
                return $(this).form('validate');
            },
            success: function(result){
                var result = eval('('+result+')');
                if(result.success){
                    $('#dlg-transaksi_turret').dialog('close');
                    //$('#grid-transaksi_turret').datagrid('reload');
                    transaksiTurretCreate();
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
    
    function transaksiTurretSaveEdit(){
        $('#fm-transaksi_turret-edit').form('submit',{
            url: url,
            onSubmit: function(){
                return $(this).form('validate');
            },
            success: function(result){
                var result = eval('('+result+')');
                if(result.success){
                    $('#dlg-transaksi_turret-edit').dialog('close');
                    $('#grid-transaksi_turret').datagrid('reload');
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
    
    function transaksiTurretHapus(){
        var row = $('#grid-transaksi_turret').datagrid('getSelected');
        if (row){
            $.messager.confirm('Konfirmasi','Anda yakin ingin menghapus Turret '+row.turret_id+' ?',function(r){
                if (r){
                    $.post('<?php echo site_url('transaksi/checksheet/turret/delete'); ?>',{turret_id:row.turret_id},function(result){
                        if (result.success){
                            $('#grid-transaksi_turret').datagrid('reload');
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
    
    function transaksiTurretImage(){
        var row = $('#grid-transaksi_turret').datagrid('getSelected');
        if (row)
        {
            $.post('<?php echo site_url('transaksi/checksheet/turret/viewImage'); ?>',{turret_id:row.turret_id},function(result){
                if (result.success){
                    var content = '<iframe scturret="auto" frameborder="0"  src="'+result.img+'" style="width:100%;height:100%;"></iframe>';
                    var title   = row.turret_id;
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

    function transaksiTurretSesdate()
    {
        $('#dlg-transaksi_turret_sesdate').dialog({modal: true}).dialog('open').dialog('setTitle','Ubah Session Date');
        $('#fm-transaksi_turret_sesdate').form('clear');
        $.post('<?php echo site_url('transaksi/checksheet/turret/getDateTurret'); ?>',function(result){
            $('#sesdate').datebox('setValue', result.sesdate);
            },'json');
    }
    
    function transaksiTurretSesdateSave()
    {
        var sesdate = $('#sesdate').datebox('getValue');
        $.post('<?php echo site_url('transaksi/checksheet/turret/updateSesdate'); ?>',{sesdate:sesdate},function(result){
            if(result.success)
            {
                $('#dlg-transaksi_turret_sesdate').dialog('close');
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
    
    function transaksiTurretAfter()
    {
        $('#dlg-transaksi_turret-after').dialog({modal: true, closable: false}).dialog('open').dialog('setTitle','Update Tgl. Turret After');
        $('#fm-transaksi_turret-after').form('clear');
        url = '<?php echo site_url('transaksi/checksheet/turret/updateAfter'); ?>';
    }
    
    function transaksiTurretAfterSave()
    {
        $('#fm-transaksi_turret-after').form('submit',{
            url: url,
            onSubmit: function(){
                return $(this).form('validate');
            },
            success: function(result){
                var result = eval('('+result+')');
                if(result.success){
                    $('#grid-transaksi_turret').datagrid('reload');
                    $('#dlg-transaksi_turret-after').dialog('close');
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
    
    function transaksiTurretBetween()
    {
        $('#dlg-transaksi_turret-between').dialog({modal: true, closable: false}).dialog('open').dialog('setTitle','Update Tgl. Turret Between');
        $('#fm-transaksi_turret-between').form('clear');
        url = '<?php echo site_url('transaksi/checksheet/turret/updateBetween'); ?>';
    }
    
    function transaksiTurretBetweenSave()
    {
        $('#fm-transaksi_turret-between').form('submit',{
            url: url,
            onSubmit: function(){
                return $(this).form('validate');
            },
            success: function(result){
                var result = eval('('+result+')');
                if(result.success){
                    $('#grid-transaksi_turret').datagrid('reload');
                    $('#dlg-transaksi_turret-between').dialog('close');
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
    
    function transaksiTurretCheck()
    {
        $('#dlg-transaksi_turret-check').dialog({modal: true, closable: false}).dialog('open').dialog('setTitle','Check Total Entry Data per Tanggal');
        $('#fm-transaksi_turret-check').form('clear');
        url = '<?php echo site_url('transaksi/checksheet/turret/check'); ?>';
    }
    
    function transaksiTurretCheckSave()
    {
        $('#fm-transaksi_turret-check').form('submit',{
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
    #fm-transaksi_turret{
        margin:0;
        padding:10px 30px;
    }
    #fm-transaksi_turret-edit{
        margin:0;
        padding:10px 30px;
    }
    #fm-transaksi_turret-after{
        margin:0;
        padding:10px 30px;
    }
    #fm-transaksi_turret-between{
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

<div id="dlg-transaksi_turret" class="easyui-dialog" style="width:650px; height:600px; padding: 10px 20px" closed="true" buttons="#dlg-buttons-transaksi_turret">
    <form id="fm-transaksi_turret" method="post" novalidate> 
        <div class="easyui-panel" data-options="style:{margin:'1% auto'}" style="position:relative;overflow:hidden;width:427px;height:240px">
            <div id="webcamTurret">
            </div>
        </div>
        <div class="center">
            <a id="captureTurret" href="javascript:void(0)" class="easyui-linkbutton" data-options="width:75" iconCls="icon-ok" onclick="captureTurret()">Capture</a>
        </div>
        <div class="grup2">
            <div class="fitem">
                <label for="type"></label>
                <input id="imgTurret" name="img" class="easyui-textbox" data-options="readonly: true"/>
            </div>
            <div class="fitem">
                <label for="type">No Lot</label>
                <input id="turret_lot" name="turret_lot" class="easyui-textbox" required="true" tabindex="1"/>
                <a id="chlotTurret" href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-search" plain="true" onclick="checkLotTurret()"></a>
            </div>
            <div class="fitem">
                <label for="type">Sub Lot</label>
                <input id="turret_sub" name="turret_sub" class="easyui-textbox" required="true" tabindex="2"/>
            </div>
            <div class="fitem">
                <label for="type">Customer</label>
                <input id="turret_customer" name="turret_customer" style="width: 300px" class="easyui-textbox" data-options="readonly: true"/>
            </div>
            <div class="fitem">
                <label for="type">Item</label>
                <input id="turret_barang" name="turret_barang" style="width: 300px" class="easyui-textbox" data-options="readonly: true"/>
            </div>
            <div class="fitem">
                <label for="type">Tanggal Turret</label>
                <input id="turret_date" name="turret_date" class="easyui-datebox" required="true"/>
            </div>
        </div>
    </form>
</div>

<!-- Dialog Button -->
<div id="dlg-buttons-transaksi_turret">
    <a href="javascript:void(0)" id="turret_oksave" class="easyui-linkbutton c1" data-options="width:75" iconCls="icon-save" tabindex="3" onclick="transaksiTurretSave()">Simpan</a>
    <a href="javascript:void(0)" class="easyui-linkbutton c5" data-options="width:75" iconCls="icon-cancel" onclick="javascript:$('#dlg-transaksi_turret').dialog('close');Webcam.reset();$('#grid-transaksi_turret').datagrid('reload');">Batal</a>
</div>

<div id="dlg-transaksi_turret-edit" class="easyui-dialog" style="width:400px; height:300px; padding: 10px 20px" closed="true" buttons="#dlg-buttons-transaksi_turret-edit">
    <form id="fm-transaksi_turret-edit" method="post" novalidate>        
        <div class="fitem">
            <label for="type">No Lot</label>
            <input type="text" id="turret_lot" name="turret_lot" class="easyui-textbox" required="true"/>
        </div>
        <div class="fitem">
            <label for="type">Sub Lot</label>
            <input type="text" id="turret_sub" name="turret_sub" class="easyui-textbox" required="true"/>
        </div>
        <div class="fitem">
            <label for="type">Tanggal Turret</label>
            <input type="text" id="turret_date" name="turret_date" class="easyui-datebox" required="true"/>
        </div>
    </form>
</div>

<!-- Dialog Button -->
<div id="dlg-buttons-transaksi_turret-edit">
    <a href="javascript:void(0)" class="easyui-linkbutton" data-options="width:75" iconCls="icon-ok" onclick="transaksiTurretSaveEdit()">Simpan</a>
    <a href="javascript:void(0)" class="easyui-linkbutton" data-options="width:75" iconCls="icon-cancel" onclick="javascript:$('#dlg-transaksi_turret-edit').dialog('close')">Batal</a>
</div>

<div id="dlg-transaksi_turret_sesdate" class="easyui-dialog" style="width:400px; height:200px; padding: 10px 20px" closed="true" buttons="#dlg-buttons-transaksi_turret_sesdate">
    <form id="fm-transaksi_turret_sesdate" method="post" novalidate>
        <div class="fitem">
            <label for="type">Session Date</label>
            <input id="sesdate" name="sesdate" class="easyui-datebox" required="true"/>
        </div>
    </form>
</div>

<!-- Dialog Button -->
<div id="dlg-buttons-transaksi_turret_sesdate">
    <a href="javascript:void(0)" class="easyui-linkbutton" data-options="width:75" iconCls="icon-ok" onclick="transaksiTurretSesdateSave()">Simpan</a>
    <a href="javascript:void(0)" class="easyui-linkbutton" data-options="width:75" iconCls="icon-cancel" onclick="javascript:$('#dlg-transaksi_turret_sesdate').dialog('close')">Batal</a>
</div>

<div id="dlg-transaksi_turret-after" class="easyui-dialog" style="width:400px; height:300px; padding: 10px 20px" closed="true" buttons="#dlg-buttons-transaksi_turret-after">
    <form id="fm-transaksi_turret-after" method="post" novalidate>        
        <div class="fitem">
            <label for="type">Tanggal Turret</label>
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
<div id="dlg-buttons-transaksi_turret-after">
    <a href="javascript:void(0)" class="easyui-linkbutton" data-options="width:75" iconCls="icon-ok" onclick="transaksiTurretAfterSave()">Simpan</a>
    <a href="javascript:void(0)" class="easyui-linkbutton" data-options="width:75" iconCls="icon-cancel" onclick="javascript:$('#dlg-transaksi_turret-after').dialog('close')">Batal</a>
</div>

<div id="dlg-transaksi_turret-between" class="easyui-dialog" style="width:400px; height:300px; padding: 10px 20px" closed="true" buttons="#dlg-buttons-transaksi_turret-between">
    <form id="fm-transaksi_turret-between" method="post" novalidate>        
        <div class="fitem">
            <label for="type">Tanggal Turret</label>
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
<div id="dlg-buttons-transaksi_turret-between">
    <a href="javascript:void(0)" class="easyui-linkbutton" data-options="width:75" iconCls="icon-ok" onclick="transaksiTurretBetweenSave()">Simpan</a>
    <a href="javascript:void(0)" class="easyui-linkbutton" data-options="width:75" iconCls="icon-cancel" onclick="javascript:$('#dlg-transaksi_turret-between').dialog('close')">Batal</a>
</div>


<div id="dlg-transaksi_turret-check" class="easyui-dialog" style="width:400px; height:200px; padding: 10px 20px" closed="true" buttons="#dlg-buttons-transaksi_turret-check">
    <form id="fm-transaksi_turret-check" method="post" novalidate>        
        <div class="fitem">
            <label for="type">Tanggal Turret</label>
            <input type="text" id="check_date" name="check_date" class="easyui-datebox" required="true"/>
        </div>
    </form>
</div>

<!-- Dialog Button -->
<div id="dlg-buttons-transaksi_turret-check">
    <a href="javascript:void(0)" class="easyui-linkbutton" data-options="width:75" iconCls="icon-ok" onclick="transaksiTurretCheckSave()">Check</a>
    <a href="javascript:void(0)" class="easyui-linkbutton" data-options="width:75" iconCls="icon-cancel" onclick="javascript:$('#dlg-transaksi_turret-check').dialog('close')">Batal</a>
</div>
<!-- End of file v_turret.php -->
<!-- Location: ./application/views/transaksi/v_turret.php -->