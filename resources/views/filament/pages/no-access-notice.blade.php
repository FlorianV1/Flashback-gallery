<div style="display: flex; align-items: center; justify-content: center; padding: 64px 16px;">
    <div style="max-width: 440px; width: 100%; text-align: center;">

        {{-- Icon --}}
        <div style="display: flex; justify-content: center; margin-bottom: 24px;">
            <div style="width: 80px; height: 80px; border-radius: 50%; display: flex; align-items: center; justify-content: center; background: #FEF0E7; border: 2px solid #F9C6A0;">
                <svg style="width: 36px; height: 36px; color: #E85D04;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
            </div>
        </div>

        {{-- Heading --}}
        <h2 style="font-size: 1.5rem; font-weight: 700; letter-spacing: -0.01em; color: #111827; margin: 0 0 12px;">
            No albums assigned yet
        </h2>

        {{-- Body --}}
        <p style="font-size: 1rem; color: #6B7280; margin: 0 0 8px; line-height: 1.6;">
            You're signed in as <strong style="color: #374151;">{{ auth()->user()->name }}</strong>,
            but you haven't been assigned to any albums yet.
        </p>
        <p style="font-size: 1rem; color: #6B7280; margin: 0 0 32px; line-height: 1.6;">
            Ask the gallery admin to assign you to an album — once they do, it'll appear here and you can start adding photos and videos.
        </p>

        {{-- Info box --}}
        <div style="background: #FEF0E7; border: 1px solid #F9C6A0; border-radius: 8px; padding: 20px 24px; text-align: left;">
            <p style="font-weight: 600; color: #C84B00; margin: 0 0 10px; font-size: 0.875rem;">How it works</p>
            <ul style="margin: 0; padding-left: 20px; color: #92400e; font-size: 0.875rem; line-height: 1.7;">
                <li>The admin assigns you to one or more albums</li>
                <li>You can upload photos and videos to those albums</li>
                <li>Your uploads appear in the public gallery instantly</li>
            </ul>
        </div>

    </div>
</div>
