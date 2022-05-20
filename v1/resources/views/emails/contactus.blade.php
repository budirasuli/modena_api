@component('mail::message')

<h3>Contact Us</h3>
<h6>This report will be sent automatically, when there is the latest contact us data transaction.</h6>

<table>
	<tr>
		<td width="30%">First Name</td>
		<td>{{ $data->value['first_name'] }}</td>
	</tr>
	<tr>
		<td width="30%">Last Name</td>
		<td>{{ $data->value['last_name'] }}</td>
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
		<td width="30%">Category</td>
		<td>{{ $data->value['category_brand'] }}</td>
	</tr>
	<tr>
		<td width="30%">Subject</td>
		<td>{{ $data->value['subject'] }}</td>
	</tr>
	<tr>
		<td width="30%">Message</td>
		<td>{{ $data->value['messages'] }}</td>
	</tr>
</table>
<hr/>
<p/>
Regards,<br>
{{ config('app.name') }}

@endcomponent