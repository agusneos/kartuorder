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
<table id="grid-transaksi_trimming"
    data-options="pageSize:50, multiSort:true, remoteSort:true, rownumbers:true, singleSelect:true, 
                fit:true, fitColumns:true, toolbar:toolbar_transaksi_trimming">
    <thead>
        <tr>
            <th data-options="field:'ck',checkbox:true" ></th>
            <th data-options="field:'trimming_id'"                 width="70" halign="center" align="center" sortable="true">No</th>
            <th data-options="field:'trimming_lot'"                   width="100" align="center" sortable="true">Lot</th>
            <th data-options="field:'trimming_sub'"                 width="70" halign="center" align="center" sortable="true">Sub Lot</th>
            <th data-options="field:'cust_name'"                 width="200" halign="center" align="left" sortable="true">Nama Customer</th>
            <th data-options="field:'item_name'"                 width="200" halign="center" align="left" sortable="true">Nama Barang</th>
            <th data-options="field:'trimming_date'"                 width="100" halign="center" align="center" sortable="true">Tanggal Trimming</th>
            <th data-options="field:'trimming_upload'"                 width="150" halign="center" align="center" sortable="true">Tanggal Capture</th>
            </tr>
    </thead>
</table>

<script type="text/javascript"> 
    var toolbar_transaksi_trimming = [{
        text:'New',
        id:'baruTrimming',
        iconCls:'icon-new_file',
        handler:function(){transaksiTrimmingCreate();}
    },{
        text:'Edit',
        iconCls:'icon-edit',
        handler:function(){transaksiTrimmingUpdate();}
    },{
        text:'Delete',
        iconCls:'icon-cancel',
        handler:function(){transaksiTrimmingHapus();}
    },{
        text:'Session Date',
        iconCls:'icon-date',
        handler:function(){transaksiTrimmingSesdate();}
    },{
        text:'Image',
        iconCls:'icon-picture',
        handler:function(){transaksiTrimmingImage();}
    },{
        text:'Refresh',
        iconCls:'icon-reload',
        handler:function(){$('#grid-transaksi_trimming').datagrid('reload');}
    },{
        text:'Update Tgl. Trimming After',
        iconCls:'icon-date',
        handler:function(){transaksiTrimmingAfter();}
    },{
        text:'Update Tgl. Trimming Between',
        iconCls:'icon-date',
        handler:function(){transaksiTrimmingBetween();}
    },{
        text:'Cek Total Entry Data',
        iconCls:'icon-date',
        handler:function(){transaksiTrimmingCheck();}
    }];
    
    $('#grid-transaksi_trimming').datagrid({view:scrollview,remoteFilter:true,
        url:'<?php echo site_url('transaksi/checksheet/trimming/index'); ?>?grid=true'})
        .datagrid('enableFilter');
        
    $(function(){	
        $('#baruTrimming').focus(); // Saat awal membuka menu lagsung fokus ke tombol new
    });
    
    function transaksiTrimmingCreate() {
        $('#dlg-transaksi_trimming').dialog({modal: true, closable: false}).dialog('open').dialog('setTitle','Tambah Data');
        $('#fm-transaksi_trimming').form('clear');
        //$('#trimming_oksave').blur();
        url = '<?php echo site_url('transaksi/checksheet/trimming/create'); ?>';
        
        $.post('<?php echo site_url('transaksi/checksheet/trimming/getDateTrimming'); ?>',function(result){
            $('#trimming_date').datebox('setValue', result.sesdate);
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
        Webcam.attach('#webcamTrimming');
        
        $('#captureTrimming').linkbutton({
            iconCls: '',
            text:'Capture'
        });
        $('#captureTrimming').focus();
        $('#captureTrimming').bind('click', function(){
            $('#captureTrimming').linkbutton({
                iconCls: 'icon-ok',
                text:'OK'
            });            
            $('#trimming_lot').textbox('setValue', '');
            $('#trimming_customer').textbox('setValue', '');
            $('#trimming_barang').textbox('setValue', '');
            $('#trimming_sub').textbox('setValue', '');
            $('#trimming_lot').next().find('input').focus();
        });
       
        $('#trimming_lot').textbox('textbox').keypress(function(e){
            if (e.which === 13){                
               $('#chlotTrimming').focus();
            }
        });
        
        $('#chlotTrimming').bind('click', function(){
            $('#trimming_sub').next().find('input').focus();
        });
        
        $('#trimming_sub').textbox('textbox').keypress(function(e){
            if (e.which === 13){
                    $('#trimming_oksave').focus();
            }
        });
    }
    
    function captureTrimming() {
        Webcam.snap( function(data_uri) {
            $('#imgTrimming').textbox('setValue',data_uri);
        });
    }
    
    function checkLotTrimming() {
        var lotidTrimming = $('#trimming_lot').textbox('getValue');
        $.post('<?php echo site_url('transaksi/checksheet/trimming/cekLot'); ?>',{trimming_lot:lotidTrimming},function(result){
            if (result.success){
                $('#trimming_sub').next().find('input').focus();
                $.post('<?php echo site_url('transaksi/checksheet/trimming/getCustItem'); ?>',{trimming_lot:lotidTrimming},function(result){
                    $('#trimming_customer').textbox('setValue', result.customer);
                    $('#trimming_barang').textbox('setValue', result.barang);
                },'json');
            } else {
                $('#trimming_lot').textbox('setValue', '');
                $('#trimming_customer').textbox('setValue', '');
                $('#trimming_barang').textbox('setValue', '');
                $('#trimming_sub').textbox('setValue', '');
                $('#trimming_lot').next().find('input').focus();
                $.messager.show({
                    title: 'Error',
                    msg: 'Lot Tidak Ditemukan'
                });
            }
        },'json');
    }
    
    function transaksiTrimmingUpdate() {
        var row = $('#grid-transaksi_trimming').datagrid('getSelected');
        if(row){
            $('#dlg-transaksi_trimming-edit').dialog({modal: true}).dialog('open').dialog('setTitle','Edit Data');
            $('#fm-transaksi_trimming-edit').form('load',row);
            url = '<?php echo site_url('transaksi/checksheet/trimming/update'); ?>/' + row.trimming_id;            
        }
        else
        {
             $.messager.alert('Info','Data belum dipilih !','info');
        }
    }
    
    function transaksiTrimmingSave(){
        $('#fm-transaksi_trimming').form('submit',{
            url: url,
            onSubmit: function(){
                return $(this).form('validate');
            },
            success: function(result){
                var result = eval('('+result+')');
                if(result.success){
                    $('#dlg-transaksi_trimming').dialog('close');
                    //$('#grid-transaksi_trimming').datagrid('reload');
                    transaksiTrimmingCreate();
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
    
    function transaksiTrimmingSaveEdit(){
        $('#fm-transaksi_trimming-edit').form('submit',{
            url: url,
            onSubmit: function(){
                return $(this).form('validate');
            },
            success: function(result){
                var result = eval('('+result+')');
                if(result.success){
                    $('#dlg-transaksi_trimming-edit').dialog('close');
                    $('#grid-transaksi_trimming').datagrid('reload');
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
    
    function transaksiTrimmingHapus(){
        var row = $('#grid-transaksi_trimming').datagrid('getSelected');
        if (row){
            $.messager.confirm('Konfirmasi','Anda yakin ingin menghapus Trimming '+row.trimming_id+' ?',function(r){
                if (r){
                    $.post('<?php echo site_url('transaksi/checksheet/trimming/delete'); ?>',{trimming_id:row.trimming_id},function(result){
                        if (result.success){
                            $('#grid-transaksi_trimming').datagrid('reload');
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
    
    function transaksiTrimmingImage(){
        var row = $('#grid-transaksi_trimming').datagrid('getSelected');
        if (row)
        {
            $.post('<?php echo site_url('transaksi/checksheet/trimming/viewImage'); ?>',{trimming_id:row.trimming_id},function(result){
                if (result.success){
                    var content = '<iframe sctrimming="auto" frameborder="0"  src="'+result.img+'" style="width:100%;height:100%;"></iframe>';
                    var title   = row.trimming_id;
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

    function transaksiTrimmingSesdate()
    {
        $('#dlg-transaksi_trimming_sesdate').dialog({modal: true}).dialog('open').dialog('setTitle','Ubah Session Date');
        $('#fm-transaksi_trimming_sesdate').form('clear');
        $.post('<?php echo site_url('transaksi/checksheet/trimming/getDateTrimming'); ?>',function(result){
            $('#sesdate').datebox('setValue', result.sesdate);
            },'json');
    }
    
    function transaksiTrimmingSesdateSave()
    {
        var sesdate = $('#sesdate').datebox('getValue');
        $.post('<?php echo site_url('transaksi/checksheet/trimming/updateSesdate'); ?>',{sesdate:sesdate},function(result){
            if(result.success)
            {
                $('#dlg-transaksi_trimming_sesdate').dialog('close');
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
    
    function transaksiTrimmingAfter()
    {
        $('#dlg-transaksi_trimming-after').dialog({modal: true, closable: false}).dialog('open').dialog('setTitle','Update Tgl. Trimming After');
        $('#fm-transaksi_trimming-after').form('clear');
        url = '<?php echo site_url('transaksi/checksheet/trimming/updateAfter'); ?>';
    }
    
    function transaksiTrimmingAfterSave()
    {
        $('#fm-transaksi_trimming-after').form('submit',{
            url: url,
            onSubmit: function(){
                return $(this).form('validate');
            },
            success: function(result){
                var result = eval('('+result+')');
                if(result.success){
                    $('#grid-transaksi_trimming').datagrid('reload');
                    $('#dlg-transaksi_trimming-after').dialog('close');
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
    
    function transaksiTrimmingBetween()
    {
        $('#dlg-transaksi_trimming-between').dialog({modal: true, closable: false}).dialog('open').dialog('setTitle','Update Tgl. Trimming Between');
        $('#fm-transaksi_trimming-between').form('clear');
        url = '<?php echo site_url('transaksi/checksheet/trimming/updateBetween'); ?>';
    }
    
    function transaksiTrimmingBetweenSave()
    {
        $('#fm-transaksi_trimming-between').form('submit',{
            url: url,
            onSubmit: function(){
                return $(this).form('validate');
            },
            success: function(result){
                var result = eval('('+result+')');
                if(result.success){
                    $('#grid-transaksi_trimming').datagrid('reload');
                    $('#dlg-transaksi_trimming-between').dialog('close');
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
    
    function transaksiTrimmingCheck()
    {
        $('#dlg-transaksi_trimming-check').dialog({modal: true, closable: false}).dialog('open').dialog('setTitle','Check Total Entry Data per Tanggal');
        $('#fm-transaksi_trimming-check').form('clear');
        url = '<?php echo site_url('transaksi/checksheet/trimming/check'); ?>';
    }
    
    function transaksiTrimmingCheckSave()
    {
        $('#fm-transaksi_trimming-check').form('submit',{
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
    #fm-transaksi_trimming{
        margin:0;
        padding:10px 30px;
    }
    #fm-transaksi_trimming-edit{
        margin:0;
        padding:10px 30px;
    }
    #fm-transaksi_trimming-after{
        margin:0;
        padding:10px 30px;
    }
    #fm-transaksi_trimming-between{
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

<div id="dlg-transaksi_trimming" class="easyui-dialog" style="width:650px; height:600px; padding: 10px 20px" closed="true" buttons="#dlg-buttons-transaksi_trimming">
    <form id="fm-transaksi_trimming" method="post" novalidate> 
        <div class="easyui-panel" data-options="style:{margin:'1% auto'}" style="position:relative;overflow:hidden;width:427px;height:240px">
            <div id="webcamTrimming">
            </div>
        </div>
        <div class="center">
            <a id="captureTrimming" href="javascript:void(0)" class="easyui-linkbutton" data-options="width:75" iconCls="icon-ok" onclick="captureTrimming()">Capture</a>
        </div>
        <div class="grup2">
            <div class="fitem">
                <label for="type"></label>
                <input id="imgTrimming" name="img" class="easyui-textbox" data-options="readonly: true"/>
            </div>
            <div class="fitem">
                <label for="type">No Lot</label>
                <input id="trimming_lot" name="trimming_lot" class="easyui-textbox" required="true" tabindex="1"/>
                <a id="chlotTrimming" href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-search" plain="true" onclick="checkLotTrimming()"></a>
            </div>
            <div class="fitem">
                <label for="type">Sub Lot</label>
                <input id="trimming_sub" name="trimming_sub" class="easyui-textbox" required="true" tabindex="2"/>
            </div>
            <div class="fitem">
                <label for="type">Customer</label>
                <input id="trimming_customer" name="trimming_customer" style="width: 300px" class="easyui-textbox" data-options="readonly: true"/>
            </div>
            <div class="fitem">
                <label for="type">Item</label>
                <input id="trimming_barang" name="trimming_barang" style="width: 300px" class="easyui-textbox" data-options="readonly: true"/>
            </div>
            <div class="fitem">
                <label for="type">Tanggal Trimming</label>
                <input id="trimming_date" name="trimming_date" class="easyui-datebox" required="true"/>
            </div>
        </div>
    </form>
</div>

<!-- Dialog Button -->
<div id="dlg-buttons-transaksi_trimming">
    <a href="javascript:void(0)" id="trimming_oksave" class="easyui-linkbutton c1" data-options="width:75" iconCls="icon-save" tabindex="3" onclick="transaksiTrimmingSave()">Simpan</a>
    <a href="javascript:void(0)" class="easyui-linkbutton c5" data-options="width:75" iconCls="icon-cancel" onclick="javascript:$('#dlg-transaksi_trimming').dialog('close');Webcam.reset();$('#grid-transaksi_trimming').datagrid('reload');">Batal</a>
</div>

<div id="dlg-transaksi_trimming-edit" class="easyui-dialog" style="width:400px; height:300px; padding: 10px 20px" closed="true" buttons="#dlg-buttons-transaksi_trimming-edit">
    <form id="fm-transaksi_trimming-edit" method="post" novalidate>        
        <div class="fitem">
            <label for="type">No Lot</label>
            <input type="text" id="trimming_lot" name="trimming_lot" class="easyui-textbox" required="true"/>
        </div>
        <div class="fitem">
            <label for="type">Sub Lot</label>
            <input type="text" id="trimming_sub" name="trimming_sub" class="easyui-textbox" required="true"/>
        </div>
        <div class="fitem">
            <label for="type">Tanggal Trimming</label>
            <input type="text" id="trimming_date" name="trimming_date" class="easyui-datebox" required="true"/>
        </div>
    </form>
</div>

<!-- Dialog Button -->
<div id="dlg-buttons-transaksi_trimming-edit">
    <a href="javascript:void(0)" class="easyui-linkbutton" data-options="width:75" iconCls="icon-ok" onclick="transaksiTrimmingSaveEdit()">Simpan</a>
    <a href="javascript:void(0)" class="easyui-linkbutton" data-options="width:75" iconCls="icon-cancel" onclick="javascript:$('#dlg-transaksi_trimming-edit').dialog('close')">Batal</a>
</div>

<div id="dlg-transaksi_trimming_sesdate" class="easyui-dialog" style="width:400px; height:200px; padding: 10px 20px" closed="true" buttons="#dlg-buttons-transaksi_trimming_sesdate">
    <form id="fm-transaksi_trimming_sesdate" method="post" novalidate>
        <div class="fitem">
            <label for="type">Session Date</label>
            <input id="sesdate" name="sesdate" class="easyui-datebox" required="true"/>
        </div>
    </form>
</div>

<!-- Dialog Button -->
<div id="dlg-buttons-transaksi_trimming_sesdate">
    <a href="javascript:void(0)" class="easyui-linkbutton" data-options="width:75" iconCls="icon-ok" onclick="transaksiTrimmingSesdateSave()">Simpan</a>
    <a href="javascript:void(0)" class="easyui-linkbutton" data-options="width:75" iconCls="icon-cancel" onclick="javascript:$('#dlg-transaksi_trimming_sesdate').dialog('close')">Batal</a>
</div>

<div id="dlg-transaksi_trimming-after" class="easyui-dialog" style="width:400px; height:300px; padding: 10px 20px" closed="true" buttons="#dlg-buttons-transaksi_trimming-after">
    <form id="fm-transaksi_trimming-after" method="post" novalidate>        
        <div class="fitem">
            <label for="type">Tanggal Trimming</label>
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
<div id="dlg-buttons-transaksi_trimming-after">
    <a href="javascript:void(0)" class="easyui-linkbutton" data-options="width:75" iconCls="icon-ok" onclick="transaksiTrimmingAfterSave()">Simpan</a>
    <a href="javascript:void(0)" class="easyui-linkbutton" data-options="width:75" iconCls="icon-cancel" onclick="javascript:$('#dlg-transaksi_trimming-after').dialog('close')">Batal</a>
</div>

<div id="dlg-transaksi_trimming-between" class="easyui-dialog" style="width:400px; height:300px; padding: 10px 20px" closed="true" buttons="#dlg-buttons-transaksi_trimming-between">
    <form id="fm-transaksi_trimming-between" method="post" novalidate>        
        <div class="fitem">
            <label for="type">Tanggal Trimming</label>
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
<div id="dlg-buttons-transaksi_trimming-between">
    <a href="javascript:void(0)" class="easyui-linkbutton" data-options="width:75" iconCls="icon-ok" onclick="transaksiTrimmingBetweenSave()">Simpan</a>
    <a href="javascript:void(0)" class="easyui-linkbutton" data-options="width:75" iconCls="icon-cancel" onclick="javascript:$('#dlg-transaksi_trimming-between').dialog('close')">Batal</a>
</div>


<div id="dlg-transaksi_trimming-check" class="easyui-dialog" style="width:400px; height:200px; padding: 10px 20px" closed="true" buttons="#dlg-buttons-transaksi_trimming-check">
    <form id="fm-transaksi_trimming-check" method="post" novalidate>        
        <div class="fitem">
            <label for="type">Tanggal Trimming</label>
            <input type="text" id="check_date" name="check_date" class="easyui-datebox" required="true"/>
        </div>
    </form>
</div>

<!-- Dialog Button -->
<div id="dlg-buttons-transaksi_trimming-check">
    <a href="javascript:void(0)" class="easyui-linkbutton" data-options="width:75" iconCls="icon-ok" onclick="transaksiTrimmingCheckSave()">Check</a>
    <a href="javascript:void(0)" class="easyui-linkbutton" data-options="width:75" iconCls="icon-cancel" onclick="javascript:$('#dlg-transaksi_trimming-check').dialog('close')">Batal</a>
</div>
<!-- End of file v_trimming.php -->
<!-- Location: ./application/views/transaksi/v_trimming.php -->