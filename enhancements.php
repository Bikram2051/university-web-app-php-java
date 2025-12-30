<?php include 'header.inc'; ?>
        <h1>Website Enhancements</h1>

        <section class="intro">
            <p>This page documents the advanced HTML and CSS features implemented on the EchoSphere website that go
                beyond the basic requirements of the assignment. Each enhancement demonstrates technical proficiency and
                improves user experience.</p>
        </section>

        <section class="enhancement">
            <h2>Enhancement 1: CSS-Only Hero Slider</h2>

            <div class="enhancement-details">
                <div class="enhancement-info">
                    <h3>Implementation Details</h3>
                    <p><strong>Location:</strong> <a href="index.php">Home Page - Hero Section</a></p>
                    <p><strong>Technology:</strong> Pure CSS with keyframe animations</p>

                    <h3>How it Exceeds Basic Requirements</h3>
                    <p>This implementation creates an engaging, automatic slideshow without any JavaScript, using
                        advanced CSS features not covered in the basic syllabus. It provides a dynamic visual experience
                        that captures user attention while maintaining excellent performance.</p>

                    <h3>Technical Implementation</h3>
                    <p>The slider is created using CSS keyframe animations to transition between slides. Each slide is
                        styled with a background image and content, with animations timed to create a smooth, continuous
                        slideshow effect.</p>

                    <h3>Code Reference</h3>
                    <pre><code>/* Automatic slideshow with CSS animations */
.slide-1 {
    animation: slideAnimation 16s infinite;
}

.slide-2 {
    animation: slideAnimation 16s infinite 4s;
}

.slide-3 {
    animation: slideAnimation 16s infinite 8s;
}

.slide-4 {
    animation: slideAnimation 16s infinite 12s;
}

@keyframes slideAnimation {
    0% {
        opacity: 0;
        z-index: 0;
    }
    5% {
        opacity: 1;
        z-index: 1;
    }
    25% {
        opacity: 1;
        z-index: 1;
    }
    30% {
        opacity: 0;
        z-index: 0;
    }
    100% {
        opacity: 0;
        z-index: 0;
    }
}</code></pre>

                    <h3>References</h3>
                    <ul>
                        <li>MDN Web Docs: <a href="https://developer.mozilla.org/en-US/docs/Web/CSS/transition"
                                target="_blank">CSS Transitions</a></li>
                        <li>CSS-Tricks: <a href="https://css-tricks.com/almanac/properties/t/transition/"
                                target="_blank">Transition Property Guide</a></li>
                    </ul>
                </div>
            </div>
        </section>

        <section class="enhancement">
            <h2>Enhancement 2: Print Stylesheet</h2>

            <div class="enhancement-details">
                <div class="enhancement-info">
                    <h3>Implementation Details</h3>
                    <p><strong>Location:</strong> All Pages (Global Print Styles)</p>
                    <p><strong>Technology:</strong> CSS3 Media Queries for Print</p>

                    <h3>How it Exceeds Basic Requirements</h3>
                    <p>This enhancement provides a printer-friendly version of the website that isn't required in the
                        basic specifications. It demonstrates consideration for different user contexts and
                        accessibility by optimizing content for physical printing, removing unnecessary elements, and
                        improving readability on paper.</p>

                    <h3>Technical Implementation</h3>
                    <p>Using <code>@media print</code> queries, the stylesheet hides non-essential elements (navigation,
                        footer, decorative images), simplifies colors to black and white, removes backgrounds, and
                        optimizes text sizing and spacing for printed output.</p>

                    <h3>Code Reference</h3>
                    <pre><code>@media print {
    header, nav, footer, .cta-button {
        display: none;
    }
    
    body {
        font-size: 12pt;
        line-height: 1.5;
        color: #000;
        background: #fff;
    }
    
    .container {
        width: 100%;
        margin: 0;
        padding: 0;
    }
    
    a::after {
        content: " (" attr(href) ")";
    }
}</code></pre>

                    <h3>References</h3>
                    <ul>
                        <li>MDN Web Docs: <a href="https://developer.mozilla.org/en-US/docs/Web/CSS/@media"
                                target="_blank">CSS Media Queries</a></li>
                        <li>Smashing Magazine: <a
                                href="https://www.smashingmagazine.com/2018/05/print-stylesheets-in-2018/"
                                target="_blank">Print Stylesheets Guide</a></li>
                    </ul>
                </div>

                <div class="enhancement-demo">
                    <h3>Testing Instructions</h3>
                    <p>To test this enhancement:</p>
                    <ol>
                        <li>Navigate to any page on the website</li>
                        <li>Open the print dialog (Ctrl+P or File → Print)</li>
                        <li>Observe the print preview showing a simplified, ink-friendly version</li>
                        <li>Note that URLs are displayed after links for reference</li>
                    </ol>
                    <div class="demo-note">
                        <p><strong>Note:</strong> This feature works across all pages of the website.</p>
                    </div>
                </div>
            </div>
        </section>

        <section class="enhancement-summary">
            <h2>Summary</h2>
            <p>These enhancements demonstrate advanced CSS techniques that improve both the visual appeal and
                functionality of the EchoSphere website. The image hover effects provide engaging user feedback, while
                the print stylesheet ensures accessibility across different media types.</p>

            <p>Both features were implemented using pure CSS without JavaScript, maintaining the assignment requirements
                while adding significant value to the user experience.</p>
        </section>
<?php include 'footer.inc'; ?>