<table>
  <thead>
    <tr>
      <th>#</th>
      <th>NIP</th>
      <th>Name</th>
      <th>Email</th>
      <th>Phone</th>
      <th>Gender</th>
      <th>Birth Date</th>
      <th>Birth Place</th>
      <th>Address</th>
      <th>City</th>
      <th>Education</th>
      <th>Division</th>
      <th>Job Title</th>
      <th>Created At</th>
      <th>Updated At</th>
      <th>Password</th>
      <th>ID</th>
    </tr>
  </thead>
  <tbody>
    @foreach ($users as $user)
      <tr>
        <td>{{ $loop->iteration }}</td>
        <td data-type="s">{{ $user->nip }}</td>
        <td>{{ $user->name }}</td>
        <td>{{ $user->email }}</td>
        <td data-type="s">{{ $user->phone }}</td>
        <td>{{ $user->gender }}</td>
        <td>{{ $user->birth_date?->format('Y-m-d') }}</td>
        <td>{{ $user->birth_place }}</td>
        <td>{{ $user->address }}</td>
        <td>{{ $user->city }}</td>
        <td>{{ $user->education?->name }}</td>
        <td>{{ $user->division?->name }}</td>
        <td>{{ $user->jobTitle?->name }}</td>
        <td>{{ $user->created_at }}</td>
        <td>{{ $user->updated_at }}</td>
        <td>{{ $user->raw_password }}</td>
        <td>{{ $user->id }}</td>
      </tr>
    @endforeach
  </tbody>
</table>
