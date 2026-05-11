import os
import base64
import requests

def test_litellm_gemini():
    # TEST WITHOUT PROXY FIRST (Direct)
    api_key = "AIzaSyCaXLGgjviKmYe3We2rekA1UPBK-Njf7oE"
    model = "gemini/gemini-1.5-flash"
    
    # Try different versions or names if possible
    # We'll use LiteLLM's local call to debug
    try:
        from litellm import completion
        
        print(f"Testing litellm local library call for {model}...")
        os.environ["GEMINI_API_KEY"] = api_key
        
        resp = completion(
            model=model,
            messages=[{"role": "user", "content": "hello"}]
        )
        print("Success for simple chat!")
        print(resp.choices[0].message.content)
        
    except Exception as e:
        print(f"Failed local call: {e}")

    # TEST PROXY CONNECTIVITY
    proxy_url = "http://localhost:4000/v1/chat/completions"
    print(f"\nTesting connection to proxy at {proxy_url}...")
    try:
        response = requests.get("http://localhost:4000/health", timeout=5)
        print(f"Proxy Health Check: {response.status_code} - {response.text}")
    except Exception as e:
        print(f"Proxy is NOT running or unreachable: {e}")

if __name__ == "__main__":
    test_litellm_gemini()
