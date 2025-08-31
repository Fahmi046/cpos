<div>
    {{-- If your happiness depends on money, you will never be happy with yourself. --}}
    <div class="p-4 bg-white rounded shadow mt-4">
        <h2 class="font-bold mb-2">Daftar Pesanan</h2>
        <table class="table-auto w-full border">
            <thead>
                <tr class="bg-gray-200">
                    <th>No SP</th>
                    <th>Tanggal</th>
                    <th>Total Item</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($pesananList as $p)
                    <tr>
                        <td>{{ $p->no_sp }}</td>
                        <td>{{ $p->tanggal }}</td>
                        <td>{{ $p->details->count() }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

</div>
