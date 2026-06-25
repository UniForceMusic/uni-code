<?php

namespace Src\Apis\Streams;

enum Type
{
    case Reasoning;
    case Answer;
    case ToolCall;
}
