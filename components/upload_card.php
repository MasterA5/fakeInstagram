<form action="./core/post/create_post.php" method="POST" enctype="multipart/form-data">

    <div class="bg-zinc-900 border border-zinc-800 rounded-2xl p-4 shadow hover:border-zinc-700 transition">

        <!-- textarea -->
        <textarea
            name="content"
            placeholder="¿Qué estás pensando?"
            class="w-full bg-transparent text-white placeholder-zinc-500 resize-none focus:outline-none text-sm"
            rows="3"></textarea>

        <!-- 📸 input imagen -->
        <div class="mt-3">
            <input
                type="file"
                name="image"
                accept="image/*"
                class="text-xs text-zinc-400 
                       file:mr-3 file:px-3 file:py-1 
                       file:rounded-lg file:border-0 
                       file:bg-indigo-600 file:text-white 
                       hover:file:bg-indigo-500">
        </div>

        <!-- 🖼 preview -->
        <img id="preview" class="mt-3 rounded-xl hidden max-h-60 object-cover">

        <!-- footer -->
        <div class="flex justify-between items-center mt-3">

            <span class="text-xs text-zinc-500">
                Comparte algo con la comunidad
            </span>

            <button
                type="submit"
                class="bg-indigo-600 hover:bg-indigo-500 text-white px-4 py-2 rounded-lg text-sm font-semibold transition-all shadow-md shadow-indigo-500/20">
                Publicar
            </button>
        </div>

    </div>

</form>