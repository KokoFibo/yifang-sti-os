<div>
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>First in</th>
                <th>First out</th>
                <th>Second in</th>
                <th>Second out</th>
                <th>total_jam_kerja</th>
                <th>total_jam_lembur</th>
                <th>gaji</th>
                <th>late</th>
            </tr>
        </thead>
        <tbody>
            @php
                $total = 0;
            @endphp
            @foreach ($data as $key => $d)
                <tr>
                    <td>{{ $d->user_id }}</td>
                    <td>{{ $d->first_in }}</td>
                    <td>{{ $d->first_out }}</td>
                    <td>{{ $d->second_in }}</td>
                    <td>{{ $d->second_out }}</td>
                    <td>{{ $d->total_jam_kerja }}</td>
                    <td>{{ $d->total_jam_lembur }}</td>
                    <td>{{ number_format(($d->gaji_pokok / 198) * 2) }}</td>
                    <td>{{ $d->late }}</td>
                </tr>
                @php
                    $total = $total + ($d->gaji_pokok / 198) * 2;
                @endphp
            @endforeach
            <tr>
                <td>Total Selisih{{ number_format($total) }} dari 5.480.135.178</td>
            </tr>
        </tbody>
    </table>
</div>
