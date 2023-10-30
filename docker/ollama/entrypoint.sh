#!/bin/sh

./bin/ollama serve &
./bin/ollama pull $MODEL

ollama run $MODEL

tail -f /dev/null
