<div>

@if($show)


<div style="
position:fixed;
inset:0;
z-index:9999;
display:flex;
align-items:center;
justify-content:center;
">


<div wire:click="close"
style="
position:absolute;
inset:0;
background:rgba(0,0,0,.5);
">
</div>




<div style="
position:relative;
width:90%;
max-width:1200px;
background:white;
border-radius:18px;
overflow:hidden;
">



<div style="
background:linear-gradient(135deg,#f97316,#ea580c);
color:white;
padding:20px;
">


<h2>
Detail Service Tahun {{ $tahun }}
</h2>


Jumlah :

<b>
{{ $this->services->count() }}
</b>

Service


</div>





<div style="
padding:20px;
overflow:auto;
">


<table style="
width:100%;
border-collapse:collapse;
">


<thead>

<tr style="background:#f3f4f6">

<th>No Asset</th>

<th>Perusahaan</th>

<th>Tanggal Masuk</th>

<th>Jenis</th>

<th>Status</th>

<th>Lama</th>

<th>Biaya</th>

</tr>


</thead>


<tbody>


@foreach($this->services as $service)


<tr>


<td>
{{ $service->asset?->NoAssetIT }}
</td>


<td>
{{ $service->asset?->perusahaan?->NamaPerusahaan }}
</td>



<td>
{{ $service->TanggalMasuk?->format('d M Y') }}
</td>



<td>
{{ $service->JenisService }}
</td>



<td>
{{ $service->StatusService }}
</td>



<td>

@if($service->TanggalSelesai)

{{ 
round(
$service->TanggalMasuk
->diffInDays(
$service->TanggalSelesai
)
)
}}

 Hari


@else

-

@endif


</td>




<td>

Rp {{ number_format(
$service->Biaya,
0,
',',
'.'
) }}

</td>



</tr>


@endforeach


</tbody>


</table>


</div>





<div style="
padding:15px;
text-align:right;
">


<button
wire:click="close"
style="
background:#374151;
color:white;
padding:10px 20px;
border-radius:10px;
border:none;
">

Tutup

</button>


</div>



</div>


</div>


@endif


</div>