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
<table id="grid-transaksi_straightening"
    data-options="pageSize:50, multiSort:true, remoteSort:true, rownumbers:true, singleSelect:true, 
                fit:true, fitColumns:true, toolbar:toolbar_transaksi_straightening">
    <thead>
        <tr>
            <th data-options="field:'ck',checkbox:true" ></th>
            <th data-options="field:'straightening_id'"                 width="70" halign="center" align="center" sortable="true">No</th>
            <th data-options="field:'straightening_lot'"                   width="100" align="center" sortable="true">Lot</th>
            <th data-options="field:'straightening_sub'"                 width="70" halign="center" align="center" sortable="true">Sub Lot</th>
            <th data-options="field:'cust_name'"                 width="200" halign="center" align="left" sortable="true">Nama Customer</th>
            <th data-options="field:'item_name'"                 width="200" halign="center" align="left" sortable="true">Nama Barang</th>
            <th data-options="field:'straightening_date'"                 width="100" halign="center" align="center" sortable="true">Tanggal Straightening</th>
            <th data-options="field:'straightening_upload'"                 width="150" halign="center" align="center" sortable="true">Tanggal Capture</th>
            </tr>
    </thead>
</table>

<script type="text/javascript"> 
    var toolbar_transaksi_straightening = [{
        text:'New',
        id:'baruStraightening',
        iconCls:'icon-new_file',
        handler:function(){transaksiStraighteningCreate();}
    },{
        text:'Edit',
        iconCls:'icon-edit',
        handler:function(){transaksiStraighteningUpdate();}
    },{
        text:'Delete',
        iconCls:'icon-cancel',
        handler:function(){transaksiStraighteningHapus();}
    },{
        text:'Session Date',
        iconCls:'icon-date',
        handler:function(){transaksiStraighteningSesdate();}
    },{
        text:'Image',
        iconCls:'icon-picture',
        handler:function(){transaksiStraighteningImage();}
    },{
        text:'Refresh',
        iconCls:'icon-reload',
        handler:function(){$('#grid-transaksi_straightening').datagrid('reload');}
    },{
        text:'Update Tgl. Straightening After',
        iconCls:'icon-date',
        handler:function(){transaksiStraighteningAfter();}
    },{
        text:'Update Tgl. Straightening Between',
        iconCls:'icon-date',
        handler:function(){transaksiStraighteningBetween();}
    },{
        text:'Cek Total Entry Data',
        iconCls:'icon-date',
        handler:function(){transaksiStraighteningCheck();}
    }];
    
    $('#grid-transaksi_straightening').datagrid({view:scrollview,remoteFilter:true,
        url:'<?php echo site_url('transaksi/checksheet/straightening/index'); ?>?grid=true'})
        .datagrid('enableFilter');
        
    $(function(){	
        $('#baruStraightening').focus(); // Saat awal membuka menu lagsung fokus ke tombol new
    });
    
    function transaksiStraighteningCreate() {
        $('#dlg-transaksi_straightening').dialog({modal: true, closable: false}).dialog('open').dialog('setTitle','Tambah Data');
        $('#fm-transaksi_straightening').form('clear');
        //$('#straightening_oksave').blur();
        url = '<?php echo site_url('transaksi/checksheet/straightening/create'); ?>';
        
        $.post('<?php echo site_url('transaksi/checksheet/straightening/getDateStraightening'); ?>',function(result){
            $('#straightening_date').datebox('setValue', result.sesdate);
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
        Webcam.attach('#webcamStraightening');
        
        $('#captureStraightening').linkbutton({
            iconCls: '',
            text:'Capture'
        });
        $('#captureStraightening').focus();
        $('#captureStraightening').bind('click', function(){
            $('#captureStraightening').linkbutton({
                iconCls: 'icon-ok',
                text:'OK'
            });            
            $('#straightening_lot').textbox('setValue', '');
            $('#straightening_customer').textbox('setValue', '');
            $('#straightening_barang').textbox('setValue', '');
            $('#straightening_sub').textbox('setValue', '');
            $('#straightening_lot').next().find('input').focus();
        });
       
        $('#straightening_lot').textbox('textbox').keypress(function(e){
            if (e.which === 13){                
               $('#chlotStraightening').focus();
            }
        });
        
        $('#chlotStraightening').bind('click', function(){
            $('#straightening_sub').next().find('input').focus();
        });
        
        $('#straightening_sub').textbox('textbox').keypress(function(e){
            if (e.which === 13){
                    $('#straightening_oksave').focus();
            }
        });
    }
    
    function captureStraightening() {
        Webcam.snap( function(data_uri) {
            $('#imgStraightening').textbox('setValue',data_uri);
        });
    }
    
    function checkLotStraightening() {
        var lotidStraightening = $('#straightening_lot').textbox('getValue');
        $.post('<?php echo site_url('transaksi/checksheet/straightening/cekLot'); ?>',{straightening_lot:lotidStraightening},function(result){
            if (result.success){
                $('#straightening_sub').next().find('input').focus();
                $.post('<?php echo site_url('transaksi/checksheet/straightening/getCustItem'); ?>',{straightening_lot:lotidStraightening},function(result){
                    $('#straightening_customer').textbox('setValue', result.customer);
                    $('#straightening_barang').textbox('setValue', result.barang);
                },'json');
            } else {
                $('#straightening_lot').textbox('setValue', '');
                $('#straightening_customer').textbox('setValue', '');
                $('#straightening_barang').textbox('setValue', '');
                $('#straightening_sub').textbox('setValue', '');
                $('#straightening_lot').next().find('input').focus();
                $.messager.show({
                    title: 'Error',
                    msg: 'Lot Tidak Ditemukan'
                });
            }
        },'json');
    }
    
    function transaksiStraighteningUpdate() {
        var row = $('#grid-transaksi_straightening').datagrid('getSelected');
        if(row){
            $('#dlg-transaksi_straightening-edit').dialog({modal: true}).dialog('open').dialog('setTitle','Edit Data');
            $('#fm-transaksi_straightening-edit').form('load',row);
            url = '<?php echo site_url('transaksi/checksheet/straightening/update'); ?>/' + row.straightening_id;            
        }
        else
        {
             $.messager.alert('Info','Data belum dipilih !','info');
        }
    }
    
    function transaksiStraighteningSave(){
        $('#fm-transaksi_straightening').form('submit',{
            url: url,
            onSubmit: function(){
                return $(this).form('validate');
            },
            success: function(result){
                var result = eval('('+result+')');
                if(result.success){
                    $('#dlg-transaksi_straightening').dialog('close');
                    //$('#grid-transaksi_straightening').datagrid('reload');
                    transaksiStraighteningCreate();
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
    
    function transaksiStraighteningSaveEdit(){
        $('#fm-transaksi_straightening-edit').form('submit',{
            url: url,
            onSubmit: function(){
                return $(this).form('validate');
            },
            success: function(result){
                var result = eval('('+result+')');
                if(result.success){
                    $('#dlg-transaksi_straightening-edit').dialog('close');
                    $('#grid-transaksi_straightening').datagrid('reload');
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
    
    function transaksiStraighteningHapus(){
        var row = $('#grid-transaksi_straightening').datagrid('getSelected');
        if (row){
            $.messager.confirm('Konfirmasi','Anda yakin ingin menghapus Straightening '+row.straightening_id+' ?',function(r){
                if (r){
                    $.post('<?php echo site_url('transaksi/checksheet/straightening/delete'); ?>',{straightening_id:row.straightening_id},function(result){
                        if (result.success){
                            $('#grid-transaksi_straightening').datagrid('reload');
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
    
    function transaksiStraighteningImage(){
        var row = $('#grid-transaksi_straightening').datagrid('getSelected');
        if (row)
        {
            $.post('<?php echo site_url('transaksi/checksheet/straightening/viewImage'); ?>',{straightening_id:row.straightening_id},function(result){
                if (result.success){
                    var content = '<iframe scstraightening="auto" frameborder="0"  src="'+result.img+'" style="width:100%;height:100%;"></iframe>';
                    var title   = row.straightening_id;
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

    function transaksiStraighteningSesdate()
    {
        $('#dlg-transaksi_straightening_sesdate').dialog({modal: true}).dialog('open').dialog('setTitle','Ubah Session Date');
        $('#fm-transaksi_straightening_sesdate').form('clear');
        $.post('<?php echo site_url('transaksi/checksheet/straightening/getDateStraightening'); ?>',function(result){
            $('#sesdate').datebox('setValue', result.sesdate);
            },'json');
    }
    
    function transaksiStraighteningSesdateSave()
    {
        var sesdate = $('#sesdate').datebox('getValue');
        $.post('<?php echo site_url('transaksi/checksheet/straightening/updateSesdate'); ?>',{sesdate:sesdate},function(result){
            if(result.success)
            {
                $('#dlg-transaksi_straightening_sesdate').dialog('close');
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
    
    function transaksiStraighteningAfter()
    {
        $('#dlg-transaksi_straightening-after').dialog({modal: true, closable: false}).dialog('open').dialog('setTitle','Update Tgl. Straightening After');
        $('#fm-transaksi_straightening-after').form('clear');
        url = '<?php echo site_url('transaksi/checksheet/straightening/updateAfter'); ?>';
    }
    
    function transaksiStraighteningAfterSave()
    {
        $('#fm-transaksi_straightening-after').form('submit',{
            url: url,
            onSubmit: function(){
                return $(this).form('validate');
            },
            success: function(result){
                var result = eval('('+result+')');
                if(result.success){
                    $('#grid-transaksi_straightening').datagrid('reload');
                    $('#dlg-transaksi_straightening-after').dialog('close');
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
    
    function transaksiStraighteningBetween()
    {
        $('#dlg-transaksi_straightening-between').dialog({modal: true, closable: false}).dialog('open').dialog('setTitle','Update Tgl. Straightening Between');
        $('#fm-transaksi_straightening-between').form('clear');
        url = '<?php echo site_url('transaksi/checksheet/straightening/updateBetween'); ?>';
    }
    
    function transaksiStraighteningBetweenSave()
    {
        $('#fm-transaksi_straightening-between').form('submit',{
            url: url,
            onSubmit: function(){
                return $(this).form('validate');
            },
            success: function(result){
                var result = eval('('+result+')');
                if(result.success){
                    $('#grid-transaksi_straightening').datagrid('reload');
                    $('#dlg-transaksi_straightening-between').dialog('close');
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
    
    function transaksiStraighteningCheck()
    {
        $('#dlg-transaksi_straightening-check').dialog({modal: true, closable: false}).dialog('open').dialog('setTitle','Check Total Entry Data per Tanggal');
        $('#fm-transaksi_straightening-check').form('clear');
        url = '<?php echo site_url('transaksi/checksheet/straightening/check'); ?>';
    }
    
    function transaksiStraighteningCheckSave()
    {
        $('#fm-transaksi_straightening-check').form('submit',{
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
    #fm-transaksi_straightening{
        margin:0;
        padding:10px 30px;
    }
    #fm-transaksi_straightening-edit{
        margin:0;
        padding:10px 30px;
    }
    #fm-transaksi_straightening-after{
        margin:0;
        padding:10px 30px;
    }
    #fm-transaksi_straightening-between{
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

<div id="dlg-transaksi_straightening" class="easyui-dialog" style="width:650px; height:600px; padding: 10px 20px" closed="true" buttons="#dlg-buttons-transaksi_straightening">
    <form id="fm-transaksi_straightening" method="post" novalidate> 
        <div class="easyui-panel" data-options="style:{margin:'1% auto'}" style="position:relative;overflow:hidden;width:427px;height:240px">
            <div id="webcamStraightening">
            </div>
        </div>
        <div class="center">
            <a id="captureStraightening" href="javascript:void(0)" class="easyui-linkbutton" data-options="width:75" iconCls="icon-ok" onclick="captureStraightening()">Capture</a>
        </div>
        <div class="grup2">
            <div class="fitem">
                <label for="type"></label>
                <input id="imgStraightening" name="img" class="easyui-textbox" data-options="readonly: true"/>
            </div>
            <div class="fitem">
                <label for="type">No Lot</label>
                <input id="straightening_lot" name="straightening_lot" class="easyui-textbox" required="true" tabindex="1"/>
                <a id="chlotStraightening" href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-search" plain="true" onclick="checkLotStraightening()"></a>
            </div>
            <div class="fitem">
                <label for="type">Sub Lot</label>
                <input id="straightening_sub" name="straightening_sub" class="easyui-textbox" required="true" tabindex="2"/>
            </div>
            <div class="fitem">
                <label for="type">Customer</label>
                <input id="straightening_customer" name="straightening_customer" style="width: 300px" class="easyui-textbox" data-options="readonly: true"/>
            </div>
            <div class="fitem">
                <label for="type">Item</label>
                <input id="straightening_barang" name="straightening_barang" style="width: 300px" class="easyui-textbox" data-options="readonly: true"/>
            </div>
            <div class="fitem">
                <label for="type">Tanggal Straightening</label>
                <input id="straightening_date" name="straightening_date" class="easyui-datebox" required="true"/>
            </div>
        </div>
    </form>
</div>

<!-- Dialog Button -->
<div id="dlg-buttons-transaksi_straightening">
    <a href="javascript:void(0)" id="straightening_oksave" class="easyui-linkbutton c1" data-options="width:75" iconCls="icon-save" tabindex="3" onclick="transaksiStraighteningSave()">Simpan</a>
    <a href="javascript:void(0)" class="easyui-linkbutton c5" data-options="width:75" iconCls="icon-cancel" onclick="javascript:$('#dlg-transaksi_straightening').dialog('close');Webcam.reset();$('#grid-transaksi_straightening').datagrid('reload');">Batal</a>
</div>

<div id="dlg-transaksi_straightening-edit" class="easyui-dialog" style="width:400px; height:300px; padding: 10px 20px" closed="true" buttons="#dlg-buttons-transaksi_straightening-edit">
    <form id="fm-transaksi_straightening-edit" method="post" novalidate>        
        <div class="fitem">
            <label for="type">No Lot</label>
            <input type="text" id="straightening_lot" name="straightening_lot" class="easyui-textbox" required="true"/>
        </div>
        <div class="fitem">
            <label for="type">Sub Lot</label>
            <input type="text" id="straightening_sub" name="straightening_sub" class="easyui-textbox" required="true"/>
        </div>
        <div class="fitem">
            <label for="type">Tanggal Straightening</label>
            <input type="text" id="straightening_date" name="straightening_date" class="easyui-datebox" required="true"/>
        </div>
    </form>
</div>

<!-- Dialog Button -->
<div id="dlg-buttons-transaksi_straightening-edit">
    <a href="javascript:void(0)" class="easyui-linkbutton" data-options="width:75" iconCls="icon-ok" onclick="transaksiStraighteningSaveEdit()">Simpan</a>
    <a href="javascript:void(0)" class="easyui-linkbutton" data-options="width:75" iconCls="icon-cancel" onclick="javascript:$('#dlg-transaksi_straightening-edit').dialog('close')">Batal</a>
</div>

<div id="dlg-transaksi_straightening_sesdate" class="easyui-dialog" style="width:400px; height:200px; padding: 10px 20px" closed="true" buttons="#dlg-buttons-transaksi_straightening_sesdate">
    <form id="fm-transaksi_straightening_sesdate" method="post" novalidate>
        <div class="fitem">
            <label for="type">Session Date</label>
            <input id="sesdate" name="sesdate" class="easyui-datebox" required="true"/>
        </div>
    </form>
</div>

<!-- Dialog Button -->
<div id="dlg-buttons-transaksi_straightening_sesdate">
    <a href="javascript:void(0)" class="easyui-linkbutton" data-options="width:75" iconCls="icon-ok" onclick="transaksiStraighteningSesdateSave()">Simpan</a>
    <a href="javascript:void(0)" class="easyui-linkbutton" data-options="width:75" iconCls="icon-cancel" onclick="javascript:$('#dlg-transaksi_straightening_sesdate').dialog('close')">Batal</a>
</div>

<div id="dlg-transaksi_straightening-after" class="easyui-dialog" style="width:400px; height:300px; padding: 10px 20px" closed="true" buttons="#dlg-buttons-transaksi_straightening-after">
    <form id="fm-transaksi_straightening-after" method="post" novalidate>        
        <div class="fitem">
            <label for="type">Tanggal Straightening</label>
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
<div id="dlg-buttons-transaksi_straightening-after">
    <a href="javascript:void(0)" class="easyui-linkbutton" data-options="width:75" iconCls="icon-ok" onclick="transaksiStraighteningAfterSave()">Simpan</a>
    <a href="javascript:void(0)" class="easyui-linkbutton" data-options="width:75" iconCls="icon-cancel" onclick="javascript:$('#dlg-transaksi_straightening-after').dialog('close')">Batal</a>
</div>

<div id="dlg-transaksi_straightening-between" class="easyui-dialog" style="width:400px; height:300px; padding: 10px 20px" closed="true" buttons="#dlg-buttons-transaksi_straightening-between">
    <form id="fm-transaksi_straightening-between" method="post" novalidate>        
        <div class="fitem">
            <label for="type">Tanggal Straightening</label>
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
<div id="dlg-buttons-transaksi_straightening-between">
    <a href="javascript:void(0)" class="easyui-linkbutton" data-options="width:75" iconCls="icon-ok" onclick="transaksiStraighteningBetweenSave()">Simpan</a>
    <a href="javascript:void(0)" class="easyui-linkbutton" data-options="width:75" iconCls="icon-cancel" onclick="javascript:$('#dlg-transaksi_straightening-between').dialog('close')">Batal</a>
</div>


<div id="dlg-transaksi_straightening-check" class="easyui-dialog" style="width:400px; height:200px; padding: 10px 20px" closed="true" buttons="#dlg-buttons-transaksi_straightening-check">
    <form id="fm-transaksi_straightening-check" method="post" novalidate>        
        <div class="fitem">
            <label for="type">Tanggal Straightening</label>
            <input type="text" id="check_date" name="check_date" class="easyui-datebox" required="true"/>
        </div>
    </form>
</div>

<!-- Dialog Button -->
<div id="dlg-buttons-transaksi_straightening-check">
    <a href="javascript:void(0)" class="easyui-linkbutton" data-options="width:75" iconCls="icon-ok" onclick="transaksiStraighteningCheckSave()">Check</a>
    <a href="javascript:void(0)" class="easyui-linkbutton" data-options="width:75" iconCls="icon-cancel" onclick="javascript:$('#dlg-transaksi_straightening-check').dialog('close')">Batal</a>
</div>
<!-- End of file v_straightening.php -->
<!-- Location: ./application/views/transaksi/v_straightening.php -->