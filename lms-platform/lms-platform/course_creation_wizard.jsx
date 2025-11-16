import React, { useMemo, useRef, useState } from "react";
import { motion, AnimatePresence } from "framer-motion";
import { Button } from "@/components/ui/button";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Dialog, DialogContent, DialogFooter, DialogHeader, DialogTitle, DialogTrigger } from "@/components/ui/dialog";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { Textarea } from "@/components/ui/textarea";
import { Tabs, TabsContent, TabsList, TabsTrigger } from "@/components/ui/tabs";
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "@/components/ui/select";
import { Badge } from "@/components/ui/badge";
import { Separator } from "@/components/ui/separator";
import {
  Plus,
  Eye,
  Save,
  Library,
  Type,
  Image as ImageIcon,
  Video,
  FileText,
  Trash2,
  ChevronRight,
  Wand2,
  LayoutTemplate,
  Move,
  MousePointerSquareDashed
} from "lucide-react";

/**
 * CourseCreationWizard.jsx
 *
 * A single-file React component that matches the uploaded "ADD-COURSE FORMAT"
 * concept: Page-1 (entry), Page-2 (builder), Page-3/4 (drill-downs), with
 * areas to add pages, drag templates, add elements (video/text/image/etc.), and
 * preview the final course. Designed to be adaptable and framework-agnostic.
 *
 * Tech: TailwindCSS + shadcn/ui + framer-motion + lucide-react.
 * All logic is self-contained; swap out UI kit easily if needed.
 */

// ---- Helper types ----
const newId = () => Math.random().toString(36).slice(2, 9);

const ELEMENT_LIBRARY = [
  { type: "text", icon: Type, label: "Text Block", defaults: { text: "Edit me…" } },
  { type: "image", icon: ImageIcon, label: "Image", defaults: { src: "https://picsum.photos/800/400", alt: "Placeholder" } },
  { type: "video", icon: Video, label: "Video", defaults: { url: "https://www.youtube.com/watch?v=dQw4w9WgXcQ" } },
  { type: "question", icon: FileText, label: "Question", defaults: { prompt: "Your question here", type: "short", options: [] } },
];

const TEMPLATE_LIBRARY = [
  {
    key: "one-question",
    label: "1-Question Page",
    icon: LayoutTemplate,
    hint: "Tek bir soru iceren basit sayfa",
    build: () => ({
      title: "Question Page",
      elements: [
        { id: newId(), type: "text", props: { text: "Answer the question below:" } },
        { id: newId(), type: "question", props: { prompt: "What is your goal for this course?", type: "long", options: [] } },
      ],
    }),
  },
  {
    key: "lesson-text-media",
    label: "Lesson: Text + Media",
    icon: LayoutTemplate,
    hint: "Metin ve media kombinasyonu",
    build: () => ({
      title: "Lesson",
      elements: [
        { id: newId(), type: "text", props: { text: "Welcome to your new lesson." } },
        { id: newId(), type: "image", props: { src: "https://picsum.photos/1200/600", alt: "Lesson visual" } },
      ],
    }),
  },
];

function LibraryItem({ item, onAdd }) {
  const Icon = item.icon;
  return (
    <button
      draggable
      onDragStart={(e) => {
        e.dataTransfer.setData("application/x-builder-element", JSON.stringify(item));
      }}
      onClick={() => onAdd(item)}
      className="group w-full flex items-center gap-3 rounded-2xl border p-3 text-left hover:shadow-md transition-shadow"
    >
      <Icon className="h-5 w-5" />
      <div className="flex-1">
        <div className="text-sm font-medium">{item.label}</div>
        {item.hint && <div className="text-xs text-muted-foreground">{item.hint}</div>}
      </div>
      <MousePointerSquareDashed className="h-4 w-4 opacity-50 group-hover:opacity-100" />
    </button>
  );
}

function RenderElement({ el, onChange, onRemove }) {
  return (
    <Card className="rounded-2xl">
      <CardContent className="p-4 space-y-3">
        <div className="flex items-center justify-between">
          <Badge variant="secondary" className="rounded-full">
            {el.type}
          </Badge>
          <Button variant="ghost" size="icon" onClick={onRemove}>
            <Trash2 className="h-4 w-4" />
          </Button>
        </div>

        {el.type === "text" && (
          <Textarea
            value={el.props.text}
            onChange={(e) => onChange({ ...el, props: { ...el.props, text: e.target.value } })}
            className="min-h-[90px]"
          />
        )}

        {el.type === "image" && (
          <div className="space-y-2">
            <Input
              value={el.props.src}
              onChange={(e) => onChange({ ...el, props: { ...el.props, src: e.target.value } })}
              placeholder="Image URL"
            />
            <Input
              value={el.props.alt}
              onChange={(e) => onChange({ ...el, props: { ...el.props, alt: e.target.value } })}
              placeholder="Alt text"
            />
            <img src={el.props.src} alt={el.props.alt} className="rounded-xl w-full object-cover" />
          </div>
        )}

        {el.type === "video" && (
          <Input
            value={el.props.url}
            onChange={(e) => onChange({ ...el, props: { ...el.props, url: e.target.value } })}
            placeholder="Video URL (YouTube, Vimeo, MP4)"
          />
        )}

        {el.type === "question" && (
          <div className="space-y-2">
            <Label>Prompt</Label>
            <Textarea
              value={el.props.prompt}
              onChange={(e) => onChange({ ...el, props: { ...el.props, prompt: e.target.value } })}
            />

            <Label>Answer Type</Label>
            <Select
              value={el.props.type}
              onValueChange={(v) => onChange({ ...el, props: { ...el.props, type: v } })}
            >
              <SelectTrigger className="w-full">
                <SelectValue />
              </SelectTrigger>
              <SelectContent>
                <SelectItem value="short">Short</SelectItem>
                <SelectItem value="long">Long</SelectItem>
                <SelectItem value="mcq">Multiple Choice</SelectItem>
              </SelectContent>
            </Select>

            {el.props.type === "mcq" && (
              <div className="space-y-2">
                {(el.props.options || []).map((opt, i) => (
                  <div key={i} className="flex items-center gap-2">
                    <Input
                      value={opt}
                      onChange={(e) => {
                        const copy = [...el.props.options];
                        copy[i] = e.target.value;
                        onChange({ ...el, props: { ...el.props, options: copy } });
                      }}
                    />
                    <Button
                      variant="ghost"
                      size="icon"
                      onClick={() => {
                        const copy = [...el.props.options];
                        copy.splice(i, 1);
                        onChange({ ...el, props: { ...el.props, options: copy } });
                      }}
                    >
                      <Trash2 className="h-4 w-4" />
                    </Button>
                  </div>
                ))}
                <Button
                  size="sm"
                  variant="secondary"
                  onClick={() => onChange({
                    ...el,
                    props: { ...el.props, options: [...(el.props.options || []), "New option"] },
                  })}
                >
                  <Plus className="h-4 w-4 mr-1" /> Add option
                </Button>
              </div>
            )}
          </div>
        )}
      </CardContent>
    </Card>
  );
}

function PageCanvas({ page, onDropNew, onUpdateElement, onRemoveElement }) {
  const dropRef = useRef(null);

  const handleDrop = (e) => {
    e.preventDefault();
    const raw = e.dataTransfer.getData("application/x-builder-element");
    if (!raw) return;
    const item = JSON.parse(raw);
    const def = ELEMENT_LIBRARY.find((x) => x.type === item.type) || item;
    const newEl = { id: newId(), type: def.type, props: { ...(def.defaults || {}) } };
    onDropNew(newEl);
  };

  return (
    <div
      ref={dropRef}
      onDragOver={(e) => e.preventDefault()}
      onDrop={handleDrop}
      className="min-h-[520px] rounded-3xl border-2 border-dashed p-4 bg-muted/30 flex flex-col gap-4"
    >
      {page.elements.length === 0 && (
        <div className="flex flex-col items-center justify-center h-[220px] gap-2 text-center">
          <Move className="h-6 w-6" />
          <p className="text-sm text-muted-foreground">
            Drag items from the left, or use the quick-add buttons below.
          </p>
        </div>
      )}

      <AnimatePresence>
        {page.elements.map((el) => (
          <motion.div
            key={el.id}
            initial={{ opacity: 0, y: 8 }}
            animate={{ opacity: 1, y: 0 }}
            exit={{ opacity: 0, y: -8 }}
            layout
          >
            <RenderElement
              el={el}
              onChange={(updated) => onUpdateElement(el.id, updated)}
              onRemove={() => onRemoveElement(el.id)}
            />
          </motion.div>
        ))}
      </AnimatePresence>
    </div>
  );
}

export default function CourseCreationWizard() {
  // Wizard steps to mirror Page-1/2/3/4 flow in the PDF
  const [step, setStep] = useState(1); // 1: Course Info, 2: Builder, 3: Templates/Details, 4: Review

  const [course, setCourse] = useState({
    title: "Untitled Course",
    subtitle: "",
    pages: [
      { id: newId(), title: "Page 1", elements: [] },
    ],
  });

  const [activePageId, setActivePageId] = useState(course.pages[0].id);
  const activePage = useMemo(
    () => course.pages.find((p) => p.id === activePageId) || course.pages[0],
    [course.pages, activePageId]
  );

  const [previewOpen, setPreviewOpen] = useState(false);
  const [addPageOpen, setAddPageOpen] = useState(false);
  const [newPageTemplate, setNewPageTemplate] = useState("none");

  // ---- Mutators ----
  const updateCourse = (patch) => setCourse((c) => ({ ...c, ...patch }));

  const updatePage = (pageId, patch) =>
    setCourse((c) => ({
      ...c,
      pages: c.pages.map((p) => (p.id === pageId ? { ...p, ...patch } : p)),
    }));

  const addElementToActive = (el) => {
    updatePage(activePage.id, { elements: [...activePage.elements, el] });
  };

  const updateElement = (elId, updated) => {
    updatePage(activePage.id, {
      elements: activePage.elements.map((e) => (e.id === elId ? updated : e)),
    });
  };

  const removeElement = (elId) => {
    updatePage(activePage.id, {
      elements: activePage.elements.filter((e) => e.id !== elId),
    });
  };

  const addBlankPage = (title = `Page ${course.pages.length + 1}`) => {
    const p = { id: newId(), title, elements: [] };
    setCourse((c) => ({ ...c, pages: [...c.pages, p] }));
    setActivePageId(p.id);
  };

  const addTemplatedPage = (tplKey) => {
    const tpl = TEMPLATE_LIBRARY.find((t) => t.key === tplKey);
    if (!tpl) return addBlankPage();
    const built = tpl.build();
    const p = { id: newId(), title: built.title, elements: built.elements };
    setCourse((c) => ({ ...c, pages: [...c.pages, p] }));
    setActivePageId(p.id);
  };

  const exportJson = () => {
    const blob = new Blob([JSON.stringify(course, null, 2)], { type: "application/json" });
    const url = URL.createObjectURL(blob);
    const a = document.createElement("a");
    a.href = url;
    a.download = `${course.title || "course"}.json`;
    a.click();
  };

  return (
    <div className="w-full p-4 md:p-8 space-y-6">
      {/* Page-1 like header with quick navigation to other pages */}
      <div className="flex flex-wrap items-center justify-between gap-3">
        <div className="space-y-1">
          <div className="text-xs uppercase tracking-wide text-muted-foreground">Add Course</div>
          <h1 className="text-2xl md:text-3xl font-semibold">{course.title || "Untitled Course"}</h1>
          <div className="text-sm text-muted-foreground">Build multi-page courses with text, images, videos, and questions.</div>
        </div>
        <div className="flex items-center gap-2">
          <Button variant="secondary" onClick={() => setStep(2)}>
            Go to Builder <ChevronRight className="ml-2 h-4 w-4" />
          </Button>
          <Button variant="ghost" onClick={exportJson}>
            <Save className="mr-2 h-4 w-4" /> Export JSON
          </Button>
          <Dialog open={previewOpen} onOpenChange={setPreviewOpen}>
            <DialogTrigger asChild>
              <Button>
                <Eye className="mr-2 h-4 w-4" /> Preview
              </Button>
            </DialogTrigger>
            <DialogContent className="max-w-4xl">
              <DialogHeader>
                <DialogTitle>Course Preview</DialogTitle>
              </DialogHeader>
              <div className="max-h-[70vh] overflow-auto space-y-6">
                {course.pages.map((p) => (
                  <Card key={p.id} className="rounded-2xl">
                    <CardHeader>
                      <CardTitle>{p.title}</CardTitle>
                    </CardHeader>
                    <CardContent className="space-y-4">
                      {p.elements.map((el) => (
                        <div key={el.id} className="space-y-2">
                          {el.type === "text" && <p className="leading-relaxed">{el.props.text}</p>}
                          {el.type === "image" && (
                            <img src={el.props.src} alt={el.props.alt} className="rounded-xl w-full object-cover" />
                          )}
                          {el.type === "video" && (
                            <div className="aspect-video w-full rounded-xl bg-black/5 grid place-items-center text-sm">
                              Video: {el.props.url}
                            </div>
                          )}
                          {el.type === "question" && (
                            <div className="space-y-2">
                              <p className="font-medium">{el.props.prompt}</p>
                              {el.props.type === "short" && <Input placeholder="Your answer" />}
                              {el.props.type === "long" && <Textarea placeholder="Your answer" />}
                              {el.props.type === "mcq" && (
                                <div className="space-y-2">
                                  {(el.props.options || []).map((opt, i) => (
                                    <label key={i} className="flex items-center gap-2">
                                      <input type="radio" name={el.id} />
                                      <span>{opt}</span>
                                    </label>
                                  ))}
                                </div>
                              )}
                            </div>
                          )}
                        </div>
                      ))}
                    </CardContent>
                  </Card>
                ))}
              </div>
              <DialogFooter>
                <Button onClick={() => setPreviewOpen(false)}>Close</Button>
              </DialogFooter>
            </DialogContent>
          </Dialog>
        </div>
      </div>

      {/* Main builder zone: Page-2 */}
      <div className="grid grid-cols-1 lg:grid-cols-12 gap-4">
        {/* Left: Library & Templates */}
        <div className="lg:col-span-3 space-y-4">
          <Card className="rounded-2xl">
            <CardHeader className="pb-2">
              <CardTitle className="flex items-center gap-2"><Library className="h-5 w-5" /> Templates</CardTitle>
            </CardHeader>
            <CardContent className="space-y-2">
              {TEMPLATE_LIBRARY.map((tpl) => (
                <LibraryItem
                  key={tpl.key}
                  item={tpl}
                  onAdd={() => addTemplatedPage(tpl.key)}
                />
              ))}
            </CardContent>
          </Card>

          <Card className="rounded-2xl">
            <CardHeader className="pb-2">
              <CardTitle className="flex items-center gap-2"><Wand2 className="h-5 w-5" /> Elements</CardTitle>
            </CardHeader>
            <CardContent className="space-y-2">
              {ELEMENT_LIBRARY.map((el) => (
                <LibraryItem key={el.type} item={el} onAdd={(item) => addElementToActive({ id: newId(), type: item.type, props: { ...(item.defaults || {}) } })} />
              ))}
            </CardContent>
          </Card>
        </div>

        {/* Center: Canvas */}
        <div className="lg:col-span-6 space-y-4">
          <Card className="rounded-2xl">
            <CardHeader className="pb-2">
              <div className="flex items-center justify-between">
                <CardTitle>{activePage?.title}</CardTitle>
                <div className="flex items-center gap-2">
                  <Button size="sm" variant="secondary" onClick={() => addElementToActive({ id: newId(), type: "text", props: { text: "New text…" } })}>
                    <Type className="mr-2 h-4 w-4" /> Text
                  </Button>
                  <Button size="sm" variant="secondary" onClick={() => addElementToActive({ id: newId(), type: "image", props: { src: "https://picsum.photos/900/500", alt: "New image" } })}>
                    <ImageIcon className="mr-2 h-4 w-4" /> Image
                  </Button>
                  <Button size="sm" variant="secondary" onClick={() => addElementToActive({ id: newId(), type: "video", props: { url: "https://example.com/video.mp4" } })}>
                    <Video className="mr-2 h-4 w-4" /> Video
                  </Button>
                  <Button size="sm" onClick={() => setPreviewOpen(true)}>
                    <Eye className="mr-2 h-4 w-4" /> Preview
                  </Button>
                </div>
              </div>
            </CardHeader>
            <CardContent>
              <PageCanvas
                page={activePage}
                onDropNew={(el) => addElementToActive(el)}
                onUpdateElement={updateElement}
                onRemoveElement={removeElement}
              />
            </CardContent>
          </Card>
        </div>

        {/* Right: Pages & Actions (Add page and preview as in PDF) */}
        <div className="lg:col-span-3 space-y-4">
          <Card className="rounded-2xl">
            <CardHeader className="pb-2">
              <CardTitle className="flex items-center justify-between">
                <span>Pages</span>
                <Button size="sm" onClick={() => setAddPageOpen(true)}>
                  <Plus className="mr-2 h-4 w-4" /> Add Page
                </Button>
              </CardTitle>
            </CardHeader>
            <CardContent className="space-y-2">
              {course.pages.map((p, idx) => (
                <button
                  key={p.id}
                  onClick={() => setActivePageId(p.id)}
                  className={`w-full rounded-xl border p-3 text-left hover:shadow ${p.id === activePageId ? "bg-primary/5 border-primary" : ""}`}
                >
                  <div className="text-sm font-medium">{p.title || `Page ${idx + 1}`}</div>
                  <div className="text-xs text-muted-foreground">{p.elements.length} element(s)</div>
                </button>
              ))}
            </CardContent>
          </Card>

          <Card className="rounded-2xl">
            <CardHeader className="pb-2">
              <CardTitle>Course Info</CardTitle>
            </CardHeader>
            <CardContent className="space-y-3">
              <Label>Title</Label>
              <Input
                value={course.title}
                onChange={(e) => updateCourse({ title: e.target.value })}
                placeholder="Course title"
              />
              <Label>Subtitle</Label>
              <Input
                value={course.subtitle}
                onChange={(e) => updateCourse({ subtitle: e.target.value })}
                placeholder="Subtitle (optional)"
              />
            </CardContent>
          </Card>
        </div>
      </div>

      {/* Add Page dialog with template selector. Choosing "1-question" mirrors the PDF note. */}
      <Dialog open={addPageOpen} onOpenChange={setAddPageOpen}>
        <DialogContent>
          <DialogHeader>
            <DialogTitle>Add a new page</DialogTitle>
          </DialogHeader>
          <Tabs defaultValue="blank" className="w-full">
            <TabsList className="grid grid-cols-2">
              <TabsTrigger value="blank">Blank</TabsTrigger>
              <TabsTrigger value="template">Templates</TabsTrigger>
            </TabsList>
            <TabsContent value="blank" className="space-y-3 pt-3">
              <Label>Page title</Label>
              <Input id="page-title" placeholder={`Page ${course.pages.length + 1}`} />
              <Button onClick={() => {
                const input = document.getElementById("page-title");
                addBlankPage(input?.value || undefined);
                setAddPageOpen(false);
              }}>
                <Plus className="mr-2 h-4 w-4" /> Create blank page
              </Button>
            </TabsContent>
            <TabsContent value="template" className="space-y-3 pt-3">
              <div className="grid grid-cols-1 sm:grid-cols-2 gap-3">
                {TEMPLATE_LIBRARY.map((tpl) => (
                  <button
                    key={tpl.key}
                    onClick={() => setNewPageTemplate(tpl.key)}
                    className={`rounded-2xl border p-3 text-left hover:shadow ${newPageTemplate === tpl.key ? "bg-primary/5 border-primary" : ""}`}
                  >
                    <div className="flex items-center gap-2">
                      <tpl.icon className="h-5 w-5" />
                      <div className="font-medium">{tpl.label}</div>
                    </div>
                    <div className="text-xs text-muted-foreground mt-1">{tpl.hint}</div>
                  </button>
                ))}
              </div>
              <Button
                onClick={() => {
                  addTemplatedPage(newPageTemplate || "one-question");
                  setAddPageOpen(false);
                }}
                disabled={!newPageTemplate}
              >
                <Plus className="mr-2 h-4 w-4" /> Use selected template
              </Button>
            </TabsContent>
          </Tabs>
        </DialogContent>
      </Dialog>

      <Separator />

      {/* Footer actions to mirror Page-4 / finalization */}
      <div className="flex flex-wrap items-center justify-between gap-3">
        <div className="text-sm text-muted-foreground">
          {course.pages.length} page(s) • {course.pages.reduce((acc, p) => acc + p.elements.length, 0)} element(s)
        </div>
        <div className="flex items-center gap-2">
          <Button variant="secondary" onClick={() => setStep(3)}>
            Review <ChevronRight className="ml-2 h-4 w-4" />
          </Button>
          <Button>
            Publish <ChevronRight className="ml-2 h-4 w-4" />
          </Button>
        </div>
      </div>
    </div>
  );
}
