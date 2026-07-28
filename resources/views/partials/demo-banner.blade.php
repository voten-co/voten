@if (config('app.demo_mode'))
    <style>
        body.demo-site {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        body.demo-site #voten-app,
        body.demo-site #backend,
        body.demo-site #bootstrap4 {
            flex: 1 1 auto;
            min-height: 0;
        }

        body.demo-site #voten-app {
            height: auto;
        }

        .demo-site-banner {
            position: relative;
            z-index: 2100;
            flex: 0 0 auto;
            box-sizing: border-box;
            width: 100%;
            padding: 10px 18px;
            border-bottom: 1px solid #d7a927;
            background: linear-gradient(90deg, #fff4bd, #ffe68a);
            color: #4b3900;
            font-family: Lato, "Trebuchet MS", sans-serif;
            font-size: 14px;
            line-height: 20px;
            text-align: center;
            box-shadow: 0 1px 5px rgba(75, 57, 0, 0.16);
        }

        .demo-site-banner strong {
            margin-right: 6px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        .demo-site-banner a {
            color: #513f00;
            font-weight: 700;
            text-decoration: underline;
            text-decoration-thickness: 1px;
            text-underline-offset: 2px;
        }

        .demo-site-banner a:hover,
        .demo-site-banner a:focus {
            color: #171200;
        }

        @media (max-width: 600px) {
            .demo-site-banner {
                padding: 8px 12px;
                font-size: 12px;
                line-height: 17px;
            }

            .demo-site-banner strong {
                display: block;
                margin-right: 0;
            }
        }
    </style>

    <aside class="demo-site-banner" role="note" aria-label="Demo environment notice">
        <strong>Special.lu archive demo</strong>
        Voten is one of our older web projects, preserved here as a demo of our past work.
        Need web design? <a href="https://special.lu/contact" target="_blank" rel="noopener">Contact Special.lu</a>.
    </aside>
@endif
