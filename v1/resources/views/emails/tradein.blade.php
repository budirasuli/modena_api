@component('mail::message')

<h3>New Customer Trade In.</h3>
<h6>This report will be sent automatically, when there is the latest trade in data transaction.</h6>

<table>
	<tr>
		<td width="30%">Number</td>
		<td>{{ $data->no_submission }}</td>
	</tr>
	<tr>
		<td width="30%">Client Name</td>
		<td>{{ $data->name }}</td>
	</tr>
	<tr>
		<td width="30%">Client Email</td>
		<td>{{ $data->email }}</td>
	</tr>
	<tr>
		<td width="30%">Client Phone</td>
		<td>{{ $data->phone }}</td>
	</tr>
	<tr>
		<td width="30%">Client Address</td>
		<td>{{ $data->address }}</td>
	</tr>
	<tr>
		<td width="30%">Province</td>
		<td>{{ $data->province->province_name }}</td>
	</tr>
	<tr>
		<td width="30%">City</td>
		<td>{{ $data->city->city_name }}</td>
	</tr>
	<tr>
		<td width="30%">District</td>
		<td>{{ $data->district->district_name }}</td>
	</tr>
	<tr>
		<td width="30%">Sub-district</td>
		<td>{{ $data->village->village_name }}, {{ $data->postal_code }}</td>
	</tr>
	@foreach($data->formTradeInProductInformation as $id => $info)
	<tr>
		<td colspan="2"><b>Product {{ $id + 1 }}</b></td>
	</tr>
	<tr>
		<td width="30%">Brand</td>
		<td>{{ $info->brand }}</td>
	</tr>
	<tr>
		<td width="30%">Category</td>
		<td>{{ $info->category->sub_category_name }}</td>
	</tr>
	{{-- @foreach($info->requests as $key => $req)
		<tr>
			<td width="30%">Request {{ $key+1 }}</td>
			<td>{{ $req }}</td>
		</tr>
	@endforeach --}}
	@if($info->image)
		<tr>
			<td width="30%">
				<img src="{{ $info->image->path }}" width="150" />
				<a href="{{ $info->image->path }}" target="_blank">View Image</a>
			</td>
			<td></td>
		</tr>
	@endif
	@endforeach
</table>
<hr/>
<p/>
Regards,<br>
{{ config('app.name') }}

@endcomponent
