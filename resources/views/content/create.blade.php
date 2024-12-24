<x-app-layout>
    <div id="loader-overlay">
        <div id="loader"></div>
    </div>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Content') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    {{ __('Create Learning Materials') }}
                </div>
            </div>
            <div class="mt-4">
                <form id="create_materials">
                    <div class="mb-4">
                        <x-input-label>
                            {{ __('Title') }}
                        </x-input-label>
                        <x-text-input id="title" class="block mt-2 w-full" type="text" name="title" required />
                    </div>
                    <div class="mb-4">
                        <x-input-label>
                            {{ __('Description') }}
                        </x-input-label>
                        <x-text-input id="description" class="block mt-2 w-full" type="text" name="description" required />
                    </div>
                    <div class="mb-4">
                        <x-input-label>
                            {{ __('Type') }}
                        </x-input-label>
                        <x-select id="type" name="type" class="mt-2" :options="$content_types" />
                    </div>
                    <div class="mb-4 text-editor-section">
                        <x-text-editor id="content" />
                        <input type="hidden" name="pdf_content" id="pdf_content" value="">
                    </div>
                    <div class="mb-4 table-section hidden">
                        <table id="data-table" class="table-auto border-collapse w-full bg-white dark:bg-gray-800 ">
                            <thead>
                                <tr>
                                    <th class="px-4 py-3 border">
                                        <x-input-label>
                                            {{ __('Question') }}
                                        </x-input-label>
                                    </th>
                                    <th class="px-4 py-3 border">
                                        <x-input-label>
                                            {{ __('Type') }}
                                        </x-input-label>
                                    </th>
                                    <th class="px-4 py-3 border">
                                        <x-input-label>
                                            {{ __('Answer') }}
                                        </x-input-label>
                                    </th>
                                    <th class="px-4 py-3 border">
                                        <x-input-label>
                                            {{ __('Actions') }}
                                        </x-input-label>
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="main-table">
                                <tr>
                                    <td class="px-4 py-2 border">
                                        <x-textarea name="question[0]"></x-textarea>
                                    </td>
                                    <td class="px-4 py-2 border">
                                        <x-select class="question-type" name="_type" :options="$question_types" />
                                    </td>
                                    <td class="short-answer px-4 py-2 border">
                                        <x-textarea name="answer[0]"></x-textarea>
                                    </td>
                                    <td class="mc-table px-4 py-2 hidden border">
                                        <table class="table-auto w-full bg-white dark:bg-gray-800">
                                            <tr>
                                                <td class="px-4 py-1">
                                                    <x-input-label>A</x-input-label>
                                                </td>
                                                <td class="px-4 py-1">
                                                    <x-textarea name="mc[0][0]"></x-textarea>
                                                </td>
                                                <td>
                                                    <input type="radio" name="correct[0]" value="A" checked class="ml-2">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="px-4 py-1">
                                                    <x-input-label>B</x-input-label>
                                                </td>
                                                <td class="px-4 py-1">
                                                    <x-textarea name="mc[0][1]"></x-textarea>
                                                </td>
                                                <td>
                                                    <input type="radio" name="correct[0]" value="B" class="ml-2">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="px-4 py-1">
                                                    <x-input-label>C</x-input-label>
                                                </td>
                                                <td class="px-4 py-1">
                                                    <x-textarea name="mc[0][2]"></x-textarea>
                                                </td>
                                                <td>
                                                    <input type="radio" name="correct[0]" value="C" class="ml-2">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="px-4 py-1">
                                                    <x-input-label>D</x-input-label>
                                                </td>
                                                <td class="px-4 py-1">
                                                    <x-textarea name="mc[0][3]"></x-textarea>
                                                </td>
                                                <td>
                                                    <input type="radio" name="correct[0]" value="B" class="ml-2">
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                    <td class="px-4 py-2 border">
                                        <x-success-button class="add-row">
                                            <i class="material-icons">&#xe145;</i>
                                        </x-success-button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <x-primary-button>
                        {{ __('Submit') }}
                    </x-primary-button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>

<style>
    #loader-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        z-index: 9999;
        display: none;
    }

    #loader {
        position: absolute;
        top: 50%;
        left: 50%;
        margin-left: -20px;
        margin-top: -20px;
        width: 40px;
        aspect-ratio: 1;
        border-radius: 50%;
        border: 5px solid #000000;
        animation: l20-1 0.8s infinite linear alternate, l20-2 1.6s infinite linear;
    }

    @keyframes l20-1 {
        0% {
            clip-path: polygon(50% 50%, 0 0, 50% 0%, 50% 0%, 50% 0%, 50% 0%, 50% 0%)
        }

        12.5% {
            clip-path: polygon(50% 50%, 0 0, 50% 0%, 100% 0%, 100% 0%, 100% 0%, 100% 0%)
        }

        25% {
            clip-path: polygon(50% 50%, 0 0, 50% 0%, 100% 0%, 100% 100%, 100% 100%, 100% 100%)
        }

        50% {
            clip-path: polygon(50% 50%, 0 0, 50% 0%, 100% 0%, 100% 100%, 50% 100%, 0% 100%)
        }

        62.5% {
            clip-path: polygon(50% 50%, 100% 0, 100% 0%, 100% 0%, 100% 100%, 50% 100%, 0% 100%)
        }

        75% {
            clip-path: polygon(50% 50%, 100% 100%, 100% 100%, 100% 100%, 100% 100%, 50% 100%, 0% 100%)
        }

        100% {
            clip-path: polygon(50% 50%, 50% 100%, 50% 100%, 50% 100%, 50% 100%, 50% 100%, 0% 100%)
        }
    }

    @keyframes l20-2 {
        0% {
            transform: scaleY(1) rotate(0deg)
        }

        49.99% {
            transform: scaleY(1) rotate(135deg)
        }

        50% {
            transform: scaleY(-1) rotate(0deg)
        }

        100% {
            transform: scaleY(-1) rotate(-135deg)
        }
    }
</style>

<script type="module">
    import {
        Editor
    } from 'https://esm.sh/@tiptap/core';
    import StarterKit from 'https://esm.sh/@tiptap/starter-kit';
    import Highlight from 'https://esm.sh/@tiptap/extension-highlight';
    import Underline from 'https://esm.sh/@tiptap/extension-underline';
    import Link from 'https://esm.sh/@tiptap/extension-link';
    import TextAlign from 'https://esm.sh/@tiptap/extension-text-align';
    import HorizontalRule from 'https://esm.sh/@tiptap/extension-horizontal-rule';
    import Image from 'https://esm.sh/@tiptap/extension-image';
    import TextStyle from 'https://esm.sh/@tiptap/extension-text-style';
    import FontFamily from 'https://esm.sh/@tiptap/extension-font-family';
    import {
        Color
    } from 'https://esm.sh/@tiptap/extension-color';
    import Bold from 'https://esm.sh/@tiptap/extension-bold';
    window.addEventListener('load', function() {
        if (document.getElementById("content")) {

            const FontSizeTextStyle = TextStyle.extend({
                addAttributes() {
                    return {
                        fontSize: {
                            default: null,
                            parseHTML: element => element.style.fontSize,
                            renderHTML: attributes => {
                                if (!attributes.fontSize) {
                                    return {};
                                }
                                return {
                                    style: 'font-size: ' + attributes.fontSize
                                };
                            },
                        },
                    };
                },
            });
            const CustomBold = Bold.extend({
                // Override the renderHTML method
                renderHTML({
                    mark,
                    HTMLAttributes
                }) {
                    const {
                        style,
                        ...rest
                    } = HTMLAttributes;

                    // Merge existing styles with font-weight
                    const newStyle = 'font-weight: bold;' + (style ? ' ' + style : '');

                    return ['span', {
                        ...rest,
                        style: newStyle.trim()
                    }, 0];
                },
                // Ensure it doesn't exclude other marks
                addOptions() {
                    return {
                        ...this.parent?.(),
                        HTMLAttributes: {},
                    };
                },
            });
            // tip tap editor setup
            const editor = new Editor({
                element: document.querySelector('#content'),
                extensions: [
                    // Exclude the default Bold mark
                    StarterKit.configure({
                        marks: {
                            bold: false,
                        },
                    }),
                    // Include the custom Bold extension
                    CustomBold,
                    TextStyle,
                    Color,
                    FontSizeTextStyle,
                    FontFamily,
                    Highlight,
                    Underline,
                    Link.configure({
                        openOnClick: false,
                        autolink: true,
                        defaultProtocol: 'https',
                    }),
                    TextAlign.configure({
                        types: ['heading', 'paragraph'],
                    }),
                    HorizontalRule,
                    Image,
                ],
                editorProps: {
                    attributes: {
                        class: 'format lg:format-lg dark:format-invert focus:outline-none format-blue max-w-none',
                    },
                }
            });


            // set up custom event listeners for the buttons
            document.getElementById('toggleBoldButton').addEventListener('click', () => editor.chain().focus().toggleBold().run());
            document.getElementById('toggleItalicButton').addEventListener('click', () => editor.chain().focus().toggleItalic().run());
            document.getElementById('toggleUnderlineButton').addEventListener('click', () => editor.chain().focus().toggleUnderline().run());
            document.getElementById('toggleStrikeButton').addEventListener('click', () => editor.chain().focus().toggleStrike().run());
            document.getElementById('toggleHighlightButton').addEventListener('click', () => {
                const isHighlighted = editor.isActive('highlight');
                // when using toggleHighlight(), judge if is is already highlighted.
                editor.chain().focus().toggleHighlight({
                    color: isHighlighted ? undefined : '#ffc078' // if is already highlighted，unset the highlight color
                }).run();
            });

            document.getElementById('toggleLinkButton').addEventListener('click', () => {
                const url = window.prompt('Enter image URL:', 'https://flowbite.com');
                editor.chain().focus().toggleLink({
                    href: url
                }).run();
            });
            document.getElementById('removeLinkButton').addEventListener('click', () => {
                editor.chain().focus().unsetLink().run()
            });
            document.getElementById('toggleCodeButton').addEventListener('click', () => {
                editor.chain().focus().toggleCode().run();
            })

            document.getElementById('toggleLeftAlignButton').addEventListener('click', () => {
                editor.chain().focus().setTextAlign('left').run();
            });
            document.getElementById('toggleCenterAlignButton').addEventListener('click', () => {
                editor.chain().focus().setTextAlign('center').run();
            });
            document.getElementById('toggleRightAlignButton').addEventListener('click', () => {
                editor.chain().focus().setTextAlign('right').run();
            });
            document.getElementById('toggleListButton').addEventListener('click', () => {
                editor.chain().focus().toggleBulletList().run();
            });
            document.getElementById('toggleOrderedListButton').addEventListener('click', () => {
                editor.chain().focus().toggleOrderedList().run();
            });
            document.getElementById('toggleBlockquoteButton').addEventListener('click', () => {
                editor.chain().focus().toggleBlockquote().run();
            });
            document.getElementById('toggleHRButton').addEventListener('click', () => {
                editor.chain().focus().setHorizontalRule().run();
            });
            document.getElementById('addImageButton').addEventListener('click', () => {
                const url = window.prompt('Enter image URL:', 'https://placehold.co/600x400');
                if (url) {
                    editor.chain().focus().setImage({
                        src: url
                    }).run();
                }
            });

            // typography dropdown
            const typographyDropdown = FlowbiteInstances.getInstance('Dropdown', 'typographyDropdown');

            document.getElementById('toggleParagraphButton').addEventListener('click', () => {
                editor.chain().focus().setParagraph().run();
                typographyDropdown.hide();
            });

            document.querySelectorAll('[data-heading-level]').forEach((button) => {
                button.addEventListener('click', () => {
                    const level = button.getAttribute('data-heading-level');
                    editor.chain().focus().toggleHeading({
                        level: parseInt(level)
                    }).run()
                    typographyDropdown.hide();
                });
            });

            const textSizeDropdown = FlowbiteInstances.getInstance('Dropdown', 'textSizeDropdown');

            // Loop through all elements with the data-text-size attribute
            document.querySelectorAll('[data-text-size]').forEach((button) => {
                button.addEventListener('click', () => {
                    const fontSize = button.getAttribute('data-text-size');

                    // Apply the selected font size via pixels using the TipTap editor chain
                    editor.chain().focus().setMark('textStyle', {
                        fontSize
                    }).run();

                    // Hide the dropdown after selection
                    textSizeDropdown.hide();
                });
            });

            // Listen for color picker changes
            const colorPicker = document.getElementById('color');
            colorPicker.addEventListener('input', (event) => {
                const selectedColor = event.target.value;

                // Apply the selected color to the selected text
                editor.chain().focus().setColor(selectedColor).run();
            })

            document.querySelectorAll('[data-hex-color]').forEach((button) => {
                button.addEventListener('click', () => {
                    const selectedColor = button.getAttribute('data-hex-color');

                    // Apply the selected color to the selected text
                    editor.chain().focus().setColor(selectedColor).run();
                });
            });

            document.getElementById('reset-color').addEventListener('click', () => {
                editor.commands.unsetColor();
            })

            const fontFamilyDropdown = FlowbiteInstances.getInstance('Dropdown', 'fontFamilyDropdown');

            // Loop through all elements with the data-font-family attribute
            document.querySelectorAll('[data-font-family]').forEach((button) => {
                button.addEventListener('click', () => {
                    const fontFamily = button.getAttribute('data-font-family');

                    // Apply the selected font size via pixels using the TipTap editor chain
                    editor.chain().focus().setFontFamily(fontFamily).run();

                    // Hide the dropdown after selection
                    fontFamilyDropdown.hide();
                });
            });

            $('#create_materials').submit(function(e) {
                e.preventDefault();

                $('#loader-overlay').show();

                var input = $("#pdf_content");
                input.attr('value', editor.getHTML());
                var formData = $(this).serialize();

                $.ajax({
                    type: 'POST',
                    url: "{{ route('content.store') }}",
                    data: formData,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        $('#loader-overlay').hide();

                        window.location.href = "{{ route('content') }}";
                    },
                    error: function(xhr) {
                        $('#loader-overlay').hide();

                        var errors = xhr.responseJSON.errors;

                        // Loop through the errors object and display each error
                        $.each(errors, function(key, value) {
                            // Display the error messages as you like
                            console.log(key + ': ' + value);
                        });
                    }
                });
            });

            if ($('html').hasClass('dark')) {
                $('#loader').css('border-color', '#FFFFFF');
            } else {
                $('#loader').css('border-color', '#000000');
            }

            var rowIndex = 1;

            function addNewRow() {
                var newRow = '<tr>' +
                    '<td class="px-4 py-2 border">' +
                    '<x-textarea name="question[' + rowIndex + ']"></x-textarea>' +
                    '</td>' +
                    '<td class="px-4 py-2 border">' +
                    `<x-select class="question-type" name="_type" :options="$question_types" />` +
                    '</td>' +
                    '<td class="short-answer px-4 py-2 border">' +
                    '<x-textarea name="answer[' + rowIndex + ']"></x-textarea>' +
                    '</td>' +
                    '<td class="mc-table px-4 py-2 hidden border">' +
                    '<table class="table-auto w-full bg-white dark:bg-gray-800">' +
                    '<tr>' +
                    '<td class="px-4 py-1">' +
                    `<x-input-label>A</x-input-label>` +
                    '</td>' +
                    '<td class="px-4 py-1">' +
                    '<x-textarea name="mc[' + rowIndex + '][0]"></x-textarea>' +
                    '</td>' +
                    '<td>' +
                    '<input type="radio" name="correct[' + rowIndex + ']" value="A" checked class="ml-2">' +
                    '</td>' +
                    '</tr>' +
                    '<tr>' +
                    '<td class="px-4 py-1">' +
                    `<x-input-label>B</x-input-label>` +
                    '</td>' +
                    '<td class="px-4 py-1">' +
                    '<x-textarea name="mc[' + rowIndex + '][1]"></x-textarea>' +
                    '</td>' +
                    '<td>' +
                    '<input type="radio" name="correct[' + rowIndex + ']" value="B" class="ml-2">' +
                    '</td>' +
                    '</tr>' +
                    '<tr>' +
                    '<td class="px-4 py-1">' +
                    `<x-input-label>C</x-input-label>` +
                    '</td>' +
                    '<td class="px-4 py-1">' +
                    '<x-textarea name="mc[' + rowIndex + '][2]"></x-textarea>' +
                    '</td>' +
                    '<td>' +
                    '<input type="radio" name="correct[' + rowIndex + ']" value="C" class="ml-2">' +
                    '</td>' +
                    '</tr>' +
                    '<tr>' +
                    '<td class="px-4 py-1">' +
                    `<x-input-label>D</x-input-label>` +
                    '</td>' +
                    '<td class="px-4 py-1">' +
                    '<x-textarea name="mc[' + rowIndex + '][3]"></x-textarea>' +
                    '</td>' +
                    '<td>' +
                    '<input type="radio" name="correct[' + rowIndex + ']" value="D" class="ml-2">' +
                    '</td>' +
                    '</tr>' +
                    '</table>' +
                    '</td>' +
                    '<td class="px-4 py-2 border">' +
                    '<table class="table-auto w-full bg-white dark:bg-gray-800">' +
                    '<tr>' +
                    '<td>' +
                    `<x-success-button class="add-row"><i class="material-icons">&#xe145;</i></x-success-button>` +
                    '</td>' +
                    '<td>' +
                    `<x-danger-button class="remove-row"><i class="material-icons">&#xe872;</i></x-danger-button>` +
                    '</td>' +
                    '</tr>' +
                    '</table>' +
                    '</td>' +
                    '</tr>';

                $('#data-table .main-table').append(newRow);
                rowIndex++; // Increment row index for the next row
            }

            $(document).on('click', '.add-row', function(event) {
                event.preventDefault(); // Prevent default click event behavior
                $(this).off('click'); // Unbind the click event
                addNewRow(); // Add a new row
            });

            $(document).on('click', '.remove-row', function() {
                $(this).closest('tr').remove();
            });

            // Listen for change in the "type" select input
            $('#type').on('change', function() {
                var selectedType = $('#type').val();
                if (selectedType === 'notes') {
                    $('.text-editor-section').show();
                    $('.table-section').hide();
                } else {
                    $('.text-editor-section').hide();
                    $('.table-section').show();
                }
            });
            $(document).on('change', '.question-type', function() {
                var selectedType = $(this).val();

                // Find the closest parent <tr> element
                var parentRow = $(this).closest('tr');

                // Find the nearest .short-answer and .mc-table elements within the same row
                var shortAnswer = parentRow.find('.short-answer');
                var mcTable = parentRow.find('.mc-table');

                if (selectedType === 'short') {
                    shortAnswer.show();
                    mcTable.hide();
                } else {
                    shortAnswer.hide();
                    mcTable.show();
                }
            });
        }
    })
</script>