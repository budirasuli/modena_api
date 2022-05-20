@component('mail::message')

<h3>MODENA Cucine</h3>
<h6>This report will be sent automatically, when there is the latest MODENA Cucine data transaction.</h6>

<table>
	<tr>
		<td width="30%">Name</td>
		<td>{{ $data->value['name'] }}</td>
	</tr>
	<tr>
		<td width="30%">Email</td>
		<td>{{ $data->value['email'] }}</td>
	</tr>
	<tr>
		<td width="30%">Phone</td>
		<td>{{ $data->value['phone'] }}</td>
	</tr>
	<tr>
		<td width="30%">Province</td>
		<td>{{ $province }}</td>
	</tr>
	<tr>
		<td width="30%">City</td>
		<td>{{ $city }}</td>
	</tr>
	<tr>
		<td width="30%">Address</td>
		<td>{{ $data->value['address'] }}</td>
	</tr>
	<tr>
		<td width="30%">Questions</td>
		<td>{{ $data->value['questions'] }}</td>
	</tr>
</table>
<hr/>
<p/>
Regards,<br>
{{ config('app.name') }}

@endcomponent