/**
 * Cloudflare Worker - OpenRouter API Proxy
 *
 * Deploy ini ke Cloudflare Workers (GRATIS) untuk bypass IP blocking
 *
 * Cara Deploy:
 * 1. Login ke https://dash.cloudflare.com
 * 2. Klik "Workers & Pages" > "Create Application" > "Create Worker"
 * 3. Copy paste script ini
 * 4. Deploy
 * 5. Copy URL worker (misal: https://openrouter-proxy.your-username.workers.dev)
 * 6. Update Laravel .env: OPENROUTER_API_URL=https://openrouter-proxy.your-username.workers.dev
 */

addEventListener('fetch', event => {
  event.respondWith(handleRequest(event.request))
})

async function handleRequest(request) {
  // CORS headers untuk allow request dari Laravel app
  const corsHeaders = {
    'Access-Control-Allow-Origin': '*',
    'Access-Control-Allow-Methods': 'GET, POST, PUT, DELETE, OPTIONS',
    'Access-Control-Allow-Headers': 'Content-Type, Authorization, HTTP-Referer, X-Title',
  }

  // Handle preflight OPTIONS request
  if (request.method === 'OPTIONS') {
    return new Response(null, {
      headers: corsHeaders
    })
  }

  // Only allow POST requests
  if (request.method !== 'POST') {
    return new Response('Method Not Allowed', {
      status: 405,
      headers: corsHeaders
    })
  }

  try {
    // Get request body and headers
    const requestBody = await request.text()
    const authHeader = request.headers.get('Authorization')

    if (!authHeader) {
      return new Response(JSON.stringify({
        error: { message: 'Authorization header required', code: 401 }
      }), {
        status: 401,
        headers: { ...corsHeaders, 'Content-Type': 'application/json' }
      })
    }

    // Forward request to OpenRouter API
    const openRouterResponse = await fetch('https://openrouter.ai/api/v1/chat/completions', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Authorization': authHeader,
        'HTTP-Referer': 'https://kitorangpeduli.id',
        'X-Title': 'Kitorang Peduli - AI Report Generator',
        'User-Agent': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'
      },
      body: requestBody
    })

    // Get response from OpenRouter
    const responseBody = await openRouterResponse.text()

    // Log for debugging (visible in Cloudflare dashboard)
    console.log('OpenRouter Status:', openRouterResponse.status)
    console.log('OpenRouter Response:', responseBody.substring(0, 200))

    // Return response with CORS headers
    return new Response(responseBody, {
      status: openRouterResponse.status,
      headers: {
        ...corsHeaders,
        'Content-Type': 'application/json'
      }
    })

  } catch (error) {
    return new Response(JSON.stringify({
      error: {
        message: 'Proxy Error: ' + error.message,
        code: 500
      }
    }), {
      status: 500,
      headers: { ...corsHeaders, 'Content-Type': 'application/json' }
    })
  }
}
