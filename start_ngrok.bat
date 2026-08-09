@echo off
echo MEMULAI NGROK TUNNEL DI PORT 8080
echo ================================
echo.
echo Menjalankan ngrok tunnel ke https://rima-nummulitic-chuffily.ngrok-free.dev ...
ngrok http 127.0.0.1:8080 --url https://rima-nummulitic-chuffily.ngrok-free.dev
pause
