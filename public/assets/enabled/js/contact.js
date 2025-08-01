document.addEventListener('DOMContentLoaded', () => {
    const copyButtons = document.querySelectorAll('.copy-button');

    copyButtons.forEach(button => {
        button.addEventListener('click', () => {
            const targetId = button.dataset.copyTarget;
            const feedbackId = button.dataset.feedbackTarget;
            const textToCopyElement = document.getElementById(targetId);
            const feedbackElement = document.getElementById(feedbackId);

            if (textToCopyElement) {
                const textToCopy = textToCopyElement.textContent.trim();

                // Use document.execCommand('copy') for better iframe compatibility
                const textarea = document.createElement('textarea');
                textarea.value = textToCopy;
                textarea.style.position = 'fixed'; // Prevent scrolling to bottom
                textarea.style.opacity = 0; // Hide it
                document.body.appendChild(textarea);
                textarea.focus();
                textarea.select();

                try {
                    const successful = document.execCommand('copy');
                    if (successful) {
                        if (feedbackElement) {
                            feedbackElement.textContent = 'Copied!';
                            feedbackElement.classList.add('show');
                            setTimeout(() => {
                                feedbackElement.classList.remove('show');
                                feedbackElement.textContent = '';
                            }, 2000); // Hide feedback after 2 seconds
                        }
                    } else {
                        console.error('Copy command was unsuccessful.');
                        if (feedbackElement) {
                            feedbackElement.textContent = 'Failed to copy.';
                            feedbackElement.classList.add('show');
                            setTimeout(() => {
                                feedbackElement.classList.remove('show');
                                feedbackElement.textContent = '';
                            }, 2000);
                        }
                    }
                } catch (err) {
                    console.error('Error attempting to copy:', err);
                    if (feedbackElement) {
                        feedbackElement.textContent = 'Failed to copy.';
                        feedbackElement.classList.add('show');
                        setTimeout(() => {
                            feedbackElement.classList.remove('show');
                            feedbackElement.textContent = '';
                        }, 2000);
                    }
                } finally {
                    document.body.removeChild(textarea);
                }
            }
        });
    });
});