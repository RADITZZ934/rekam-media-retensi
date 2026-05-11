@echo off
echo MENGINSTALL DAN MENJALANKAN LITELLM PROXY
echo =========================================
echo.
echo Sedang menginstall/memastikan LiteLLM (dengan proxy) tersedia...
python -m pip install "litellm[proxy]"

echo.
echo =========================================
echo PERHATIAN: Masukkan API Key Gemini Anda di bawah ini
echo =========================================
echo.
set GEMINI_API_KEY=AIzaSyCaXLGgjviKmYe3We2rekA1UPBK-Njf7oE
set GOOGLE_API_KEY=AIzaSyCaXLGgjviKmYe3We2rekA1UPBK-Njf7oE
set GEMINI_API_VERSION=v1

echo.
echo Memulai LiteLLM Proxy di http://localhost:4000 ...
echo Konfigurasi model gemini/gemini-1.5-flash dengan API Version: v1
"%USERPROFILE%\AppData\Local\Python\pythoncore-3.14-64\Scripts\litellm.exe" --model gemini/gemini-1.5-flash-latest --model_alias "gemini-1.5-flash:gemini/gemini-1.5-flash-latest" --detailed_debug
pause
