<div wire:loading
    class="fixed top-0 left-0 w-full h-1 bg-gradient-to-r from-sky-400 via-blue-500 to-cyan-600 animate-[progress_1.5s_ease-in-out_infinite] z-50">
</div>

<style>
    @keyframes progress {
        0% {
            transform: translateX(-100%);
        }

        100% {
            transform: translateX(100%);
        }
    }
</style>
