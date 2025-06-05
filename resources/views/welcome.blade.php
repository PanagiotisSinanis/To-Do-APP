@extends("layout.home")

@section("content")
<main class="flex-shrink-0 mt-5">
    <div class="container" style="max-width: 600px">

        {{-- Flash Messages --}}
        @if(session('success'))
            <div class="alert alert-success mt-3">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger mt-3">{{ session('error') }}</div>
        @endif

        <div class="my-3 p-3 bg-body rounded shadow-sm">
            <h6 class="border-bottom pb-2 mb-0">List Of Tasks</h6>

            @foreach($tasks as $task)
                <div class="d-flex text-body-secondary pt-3">
                    {{-- Icon --}}
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                         viewBox="0 0 24 24" fill="currentColor"
                         class="icon icon-tabler icons-tabler-filled icon-tabler-direction-sign">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <path d="M10.52 2.614a2.095 2.095 0 0 1 2.835 -.117l.126 .117l7.905 7.905c.777 .777 .816 2.013 .117 2.836l-.117 .126l-7.905 7.905a2.094 2.094 0 0 1 -2.836 .117l-.126 -.117l-7.907 -7.906a2.096 2.096 0 0 1 -.115 -2.835l.117 -.126l7.905 -7.905zm5.969 9.535l.01 -.116l-.003 -.12l-.016 -.114l-.03 -.11l-.044 -.112l-.052 -.098l-.076 -.105l-.07 -.081l-3.5 -3.5l-.095 -.083a1 1 0 0 0 -1.226 0l-.094 .083l-.083 .094a1 1 0 0 0 0 1.226l.083 .094l1.792 1.793h-5.085l-.117 .007a1 1 0 0 0 0 1.986l.117 .007h5.085l-1.792 1.793l-.083 .094a1 1 0 0 0 1.403 1.403l.094 -.083l3.5 -3.5l.097 -.112l.05 -.074l.037 -.067l.05 -.112l.023 -.076l.025 -.117z"/>
                    </svg>

                    {{-- Task Content --}}
                    <div class="pb-3 mb-0 small lh-sm border-bottom w-100">
                        <div class="d-flex justify-content-between">
                            <strong class="text-gray-dark">{{ $task->title }} | {{ $task->deadline }}</strong>
                            <div class="d-flex gap-1">
                                {{-- Complete Task Form --}}
                                <form method="POST" action="{{ route('task.status.update', $task->id) }}">
                                    @csrf
                                    <button type="submit" class="btn btn-success btn-sm" title="Complete Task">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2"
                                             stroke-linecap="round" stroke-linejoin="round"
                                             class="icon icon-tabler icon-tabler-check">
                                            <path stroke="none" d="M0 0h24v24H0z"/>
                                            <path d="M5 12l5 5l10 -10"/>
                                        </svg>
                                    </button>
                                </form>

                                {{-- Delete Task Form --}}
                                <form method="POST" action="{{ route('task.delete', $task->id) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" title="Delete Task">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor"
                                             class="icon icon-tabler icon-tabler-trash-filled">
                                            <path d="M20 6a1 1 0 0 1 .117 1.993l-.117 .007h-.081l-.919 11a3 3 0 0 1-2.824 2.995l-.176 .005h-8a3 3 0 0 1-2.997-2.75l-.005-.167l-.923-11.083h-.08a1 1 0 0 1-.117-1.993l.117-.007h16z"/>
                                            <path d="M14 2a2 2 0 0 1 2 2a1 1 0 0 1-1.993 .117l-.007-.117h-4l-.007 .117a1 1 0 0 1-1.993-.117a2 2 0 0 1 1.85-1.995l.15-.005h4z"/>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                        <span class="d-block">{{ $task->description }}</span>
                    </div>
                </div>
            @endforeach

            <small class="d-block text-end mt-3">
                {{-- Optional pagination or footer --}}
            </small>
        </div>
    </div>
</main>
@endsection
