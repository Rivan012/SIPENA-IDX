<script setup>
import { computed, ref, watch } from 'vue';
import { EditorContent, useEditor } from '@tiptap/vue-3';
import { Color, TextStyle } from '@tiptap/extension-text-style';
import Image from '@tiptap/extension-image';
import Link from '@tiptap/extension-link';
import { TableKit } from '@tiptap/extension-table';
import TextAlign from '@tiptap/extension-text-align';
import StarterKit from '@tiptap/starter-kit';
import Underline from '@tiptap/extension-underline';

const props = defineProps({
    modelValue: {
        type: String,
        default: '',
    },
    uploadUrl: {
        type: String,
        required: true,
    },
    disabled: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(['update:modelValue']);
const imageInput = ref(null);
const uploadError = ref('');
const uploading = ref(false);
const linkUrl = ref('');
const textColor = ref('#1f2937');

const editor = useEditor({
    content: props.modelValue || '',
    editable: !props.disabled,
    extensions: [
        StarterKit.configure({
            code: false,
            codeBlock: false,
            link: false,
            underline: false,
        }),
        Link.configure({
            openOnClick: false,
            protocols: ['http', 'https', 'mailto'],
        }),
        Underline,
        TextStyle,
        Color,
        TextAlign.configure({ types: ['heading', 'paragraph'] }),
        TableKit.configure({
            table: {
                resizable: false,
            },
        }),
        Image.configure({
            allowBase64: false,
            inline: false,
        }),
    ],
    onUpdate: ({ editor: currentEditor }) => {
        emit('update:modelValue', currentEditor.isEmpty ? '' : currentEditor.getHTML());
    },
});

const currentHeading = computed(() => {
    if (editor.value?.isActive('heading', { level: 1 })) return '1';
    if (editor.value?.isActive('heading', { level: 2 })) return '2';
    if (editor.value?.isActive('heading', { level: 3 })) return '3';

    return 'paragraph';
});

watch(() => props.modelValue, (value) => {
    const currentEditor = editor.value;

    if (currentEditor && currentEditor.getHTML() !== (value || '')) {
        currentEditor.commands.setContent(value || '', { emitUpdate: false });
    }
});

watch(() => props.disabled, (disabled) => {
    editor.value?.setEditable(!disabled);
});

const setHeading = (event) => {
    const value = event.target.value;
    if (!editor.value) return;

    if (value === 'paragraph') {
        editor.value.chain().focus().setParagraph().run();
    } else {
        editor.value.chain().focus().toggleHeading({ level: Number(value) }).run();
    }
};

const applyLink = () => {
    const currentEditor = editor.value;
    const candidate = linkUrl.value.trim();
    if (!currentEditor || !candidate) return;

    try {
        const parsed = new URL(candidate);
        if (!['http:', 'https:', 'mailto:'].includes(parsed.protocol)) return;

        currentEditor.chain().focus().extendMarkRange('link').setLink({ href: candidate }).run();
        linkUrl.value = '';
    } catch {
        return;
    }
};

const removeLink = () => {
    editor.value?.chain().focus().unsetLink().run();
};

const openImagePicker = () => {
    imageInput.value?.click();
};

const uploadImage = async (event) => {
    const file = event.target.files?.[0];
    event.target.value = '';
    uploadError.value = '';

    if (!file || !editor.value) return;

    const formData = new FormData();
    formData.append('image', file);
    uploading.value = true;

    try {
        const xsrfToken = document.cookie
            .split('; ')
            .find((cookie) => cookie.startsWith('XSRF-TOKEN='))
            ?.split('=')[1];
        const response = await fetch(props.uploadUrl, {
            method: 'POST',
            credentials: 'same-origin',
            headers: {
                Accept: 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                ...(xsrfToken ? { 'X-XSRF-TOKEN': decodeURIComponent(xsrfToken) } : {}),
            },
            body: formData,
        });
        const result = await response.json();

        if (!response.ok) {
            throw new Error(result.errors?.image?.[0] || result.message || 'Gambar gagal diunggah.');
        }

        editor.value.chain().focus().setImage({ src: result.src, alt: result.alt || 'Gambar kegiatan' }).run();
    } catch (error) {
        uploadError.value = error.message || 'Gambar gagal diunggah.';
    } finally {
        uploading.value = false;
    }
};
</script>

<template>
    <div class="rich-text-editor" :class="disabled ? 'rich-text-editor-disabled' : ''">
        <div class="rich-text-toolbar" role="toolbar" aria-label="Format deskripsi kegiatan">
            <select :value="currentHeading" :disabled="disabled" aria-label="Gaya paragraf" @change="setHeading" class="rich-text-select">
                <option value="paragraph">Paragraf</option>
                <option value="1">Heading 1</option>
                <option value="2">Heading 2</option>
                <option value="3">Heading 3</option>
            </select>

            <button type="button" :disabled="disabled" :aria-pressed="editor?.isActive('bold')" aria-label="Tebal" title="Tebal" class="rich-text-tool rich-text-bold" @click="editor?.chain().focus().toggleBold().run()">B</button>
            <button type="button" :disabled="disabled" :aria-pressed="editor?.isActive('italic')" aria-label="Miring" title="Miring" class="rich-text-tool rich-text-italic" @click="editor?.chain().focus().toggleItalic().run()">I</button>
            <button type="button" :disabled="disabled" :aria-pressed="editor?.isActive('underline')" aria-label="Garis bawah" title="Garis bawah" class="rich-text-tool rich-text-underline" @click="editor?.chain().focus().toggleUnderline().run()">U</button>

            <button type="button" :disabled="disabled" :aria-pressed="editor?.isActive('bulletList')" aria-label="Daftar bullet" title="Daftar bullet" class="rich-text-tool" @click="editor?.chain().focus().toggleBulletList().run()">• Daftar</button>
            <button type="button" :disabled="disabled" :aria-pressed="editor?.isActive('orderedList')" aria-label="Daftar bernomor" title="Daftar bernomor" class="rich-text-tool" @click="editor?.chain().focus().toggleOrderedList().run()">1. Daftar</button>

            <label class="rich-text-color" title="Warna teks">
                <span>Warna</span>
                <input v-model="textColor" :disabled="disabled" type="color" aria-label="Pilih warna teks" @input="editor?.chain().focus().setColor(textColor).run()" />
            </label>

            <div class="rich-text-separator" aria-hidden="true"></div>
            <button type="button" :disabled="disabled" aria-label="Rata kiri" title="Rata kiri" class="rich-text-tool" @click="editor?.chain().focus().setTextAlign('left').run()">Kiri</button>
            <button type="button" :disabled="disabled" aria-label="Rata tengah" title="Rata tengah" class="rich-text-tool" @click="editor?.chain().focus().setTextAlign('center').run()">Tengah</button>
            <button type="button" :disabled="disabled" aria-label="Rata kanan" title="Rata kanan" class="rich-text-tool" @click="editor?.chain().focus().setTextAlign('right').run()">Kanan</button>

            <div class="rich-text-separator" aria-hidden="true"></div>
            <button type="button" :disabled="disabled" class="rich-text-tool" @click="editor?.chain().focus().insertTable({ rows: 3, cols: 3, withHeaderRow: true }).run()">Tabel</button>
            <button type="button" :disabled="disabled || !editor?.isActive('table')" class="rich-text-tool" @click="editor?.chain().focus().addRowAfter().run()">+ Baris</button>
            <button type="button" :disabled="disabled || !editor?.isActive('table')" class="rich-text-tool" @click="editor?.chain().focus().addColumnAfter().run()">+ Kolom</button>
            <button type="button" :disabled="disabled || !editor?.isActive('table')" class="rich-text-tool" @click="editor?.chain().focus().deleteTable().run()">Hapus tabel</button>

            <input ref="imageInput" class="sr-only" type="file" accept="image/jpeg,image/png,image/webp" @change="uploadImage" />
            <button type="button" :disabled="disabled || uploading" class="rich-text-tool" @click="openImagePicker">
                {{ uploading ? 'Mengunggah…' : 'Gambar' }}
            </button>
        </div>

        <div class="rich-text-link-tools">
            <label class="sr-only" for="rich-text-link-url">Alamat tautan</label>
            <input id="rich-text-link-url" v-model="linkUrl" :disabled="disabled" type="text" inputmode="url" placeholder="https://alamat-tautan" class="rich-text-link-input" />
            <button type="button" :disabled="disabled || !linkUrl" class="rich-text-tool" @click="applyLink">Tautkan teks terpilih</button>
            <button type="button" :disabled="disabled || !editor?.isActive('link')" class="rich-text-tool" @click="removeLink">Hapus tautan</button>
        </div>

        <EditorContent :editor="editor" class="rich-text-editor-content" />
        <p v-if="uploadError" class="rich-text-error" role="alert">{{ uploadError }}</p>
        <p class="rich-text-hint">Gambar maksimal 5 MB. Format yang didukung: JPG, PNG, WebP.</p>
    </div>
</template>
