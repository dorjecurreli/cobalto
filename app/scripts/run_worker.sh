#!/bin/bash

INTERVAL=10
LIMIT=10
TIME_LIMIT=3600
MEMORY_LIMIT=128M
SLEEP=5
FAILURE_LIMIT=5

while true; do
    echo "Starting Symfony worker at $(date)"

    php bin/console messenger:consume async --limit=$LIMIT --time-limit=$TIME_LIMIT --memory-limit=$MEMORY_LIMIT --sleep=$SLEEP --failure-limit=$FAILURE_LIMIT

    EXIT_CODE=$?

    if [ $EXIT_CODE -ne 0 ]; then
        echo "Worker encountered an error (exit code: $EXIT_CODE) at $(date). Restarting after $INTERVAL seconds."
    else
        echo "Worker finished without errors at $(date). Sleeping for $INTERVAL seconds."
    fi

    sleep $INTERVAL
done
